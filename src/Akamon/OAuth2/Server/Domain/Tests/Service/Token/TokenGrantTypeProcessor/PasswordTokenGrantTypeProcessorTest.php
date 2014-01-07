<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Model\UserCredentials;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;

class PasswordTokenGrantTypeProcessorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $userCredentialsChecker;
    /** @var MockInterface */
    private $userIdObtainer;
    /** @var MockInterface */
    private $scopesObtainer;
    /** @var MockInterface */
    private $tokenCreator;

    /** @var PasswordTokenGrantTypeProcessor */
    private $processor;

    protected function setUp()
    {
        $this->userCredentialsChecker = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface');
        $this->userIdObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\UserIdObtainerInterface');
        $this->scopesObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface');
        $this->tokenCreator = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface');

        $this->processor = new PasswordTokenGrantTypeProcessor($this->userCredentialsChecker, $this->userIdObtainer, $this->scopesObtainer, $this->tokenCreator);
    }

    public function testGetGrantTypeShouldReturnPassword()
    {
        $this->assertSame('password', $this->processor->getGrantType());
    }

    public function testProcessOk()
    {
        $client = $this->createClient();

        $userId = 'foo';
        $username = 'pablodip';
        $password = 'pass';
        $userCredentials = new UserCredentials($username, $password);

        $scope = 'all';
        $scopes = new ScopeCollection([new Scope($scope)]);

        $context = new Context($client, $userId, $scopes);

        $inputData = ['username' => $username, 'password' => $password, 'scope' => $scope];
        $parameters = new \stdClass();

        $this->userCredentialsChecker->shouldReceive('check')->once()->with(\Mockery::on(function ($v) use ($userCredentials) { return $v == $userCredentials; }))->andReturn(true);
        $this->userIdObtainer->shouldReceive('getUserId')->once()->with($username)->andReturn($userId);
        $this->scopesObtainer->shouldReceive('getScopes')->once()->with($inputData)->andReturn($scopes);
        $this->tokenCreator->shouldReceive('create')->once()->with(\Mockery::on(function ($v) use ($context) { return $v == $context; }))->andReturn($parameters);

        $this->assertSame($parameters, $this->processor->process($client, $inputData));
    }

    /**
     * @dataProvider providerUserCredentialsNotFound
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\UserCredentialsNotFoundException
     */
    public function testGrantShouldThrowAnUserCredentialsNotFoundException($inputData)
    {
        $this->processor->process($this->createClient(), $inputData);
    }

    public function providerUserCredentialsNotFound()
    {
        return [
            [[]],
            [['username' => 'foo']],
            [['password' => 'foo']]
        ];
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException
     */
    public function testGrantShouldThrowAnInvalidUserCredentialsException()
    {
        $username = 'user';
        $password = 'pass';
        $userCredentials = new UserCredentials($username, $password);

        $inputData = ['username' => $username, 'password' => $password];

        $this->userCredentialsChecker->shouldReceive('check')->once()->with(\Mockery::on(function ($v) use ($userCredentials) { return $v == $userCredentials; }))->andReturn(false);

        $this->processor->process($this->createClient(), $inputData);
    }
}
