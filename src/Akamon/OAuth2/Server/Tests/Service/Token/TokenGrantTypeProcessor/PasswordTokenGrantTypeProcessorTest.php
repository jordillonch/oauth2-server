<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Model\UserCredentials;
use Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Symfony\Component\HttpFoundation\Request;
use Mockery\MockInterface;

class PasswordTokenGrantTypeProcessorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $contextObtainer;
    /** @var MockInterface */
    private $userCredentialsChecker;
    /** @var MockInterface */
    private $tokenCreator;

    /** @var PasswordTokenGrantTypeProcessor */
    private $processor;

    protected function setUp()
    {
        $this->contextObtainer = $this->mock('Akamon\OAuth2\Server\Service\ContextObtainer\ContextObtainerInterface');
        $this->userCredentialsChecker = $this->mock('Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface');
        $this->tokenCreator = $this->mock('Akamon\OAuth2\Server\Service\Token\TokenCreator\TokenCreatorInterface');

        $this->processor = new PasswordTokenGrantTypeProcessor($this->contextObtainer, $this->userCredentialsChecker, $this->tokenCreator);
    }

    public function testGetGrantTypeShouldReturnPassword()
    {
        $this->assertSame('password', $this->processor->getGrantType());
    }

    public function testProcessOk()
    {
        $username = 'pablodip';
        $password = 'pass';
        $userCredentials = new UserCredentials($username, $password);

        $context = $this->createContextMock();

        $request = $this->createAuthenticatedRequest($username, $password);
        $response = new \stdClass();

        $this->contextObtainer->shouldReceive('getContext')->once()->with($request, \Mockery::any())->andReturn($context);
        $this->userCredentialsChecker->shouldReceive('check')->once()->with(\Mockery::on(function ($v) use ($userCredentials) { return $v == $userCredentials; }))->andReturn(true);
        $this->tokenCreator->shouldReceive('create')->once()->with($context)->andReturn($response);

        $this->assertSame($response, $this->processor->process($request));
    }

    /**
     * @dataProvider providerUserCredentialsNotFound
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\UserCredentialsNotFoundException
     */
    public function testGrantShouldThrowAnUserCredentialsNotFoundException($request)
    {
        $context = $this->createContextMock();
        $this->contextObtainer->shouldReceive('getContext')->once()->with($request, \Mockery::any())->andReturn($context);

        $this->processor->process($request);
    }

    public function providerUserCredentialsNotFound()
    {
        return [
            [new Request()],
            [new Request([], ['username' => 'foo'])],
            [new Request([], ['password' => 'foo'])]
        ];
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException
     */
    public function testGrantShouldThrowAnInvalidUserCredentialsException()
    {
        $username = 'user';
        $password = 'pass';
        $userCredentials = new UserCredentials($username, $password);

        $context = $this->createContextMock();

        $request = $this->createAuthenticatedRequest($username, $password);

        $this->contextObtainer->shouldReceive('getContext')->once()->with($request, \Mockery::any())->andReturn($context);
        $this->userCredentialsChecker->shouldReceive('check')->once()->with(\Mockery::on(function ($v) use ($userCredentials) { return $v == $userCredentials; }))->andReturn(false);

        $this->processor->process($request);
    }

    private function createAuthenticatedRequest($username, $password, $scope = '')
    {
        $query = [];
        $request = ['username' => $username, 'password' => $password, 'scope' => $scope];

        return new Request($query, $request);
    }
}
