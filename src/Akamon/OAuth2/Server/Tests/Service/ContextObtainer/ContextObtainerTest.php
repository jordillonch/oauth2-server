<?php

namespace Akamon\OAuth2\Server\Tests\Service\ContextObtainer;

use Akamon\OAuth2\Server\Service\ContextObtainer\ContextObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;

class ContextObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $clientObtainer;
    /** @var MockInterface */
    private $userIdObtainer;
    /** @var MockInterface */
    private $scopeObtainer;

    /** @var ContextObtainer */
    private $contextObtainer;

    protected function setUp()
    {
        $this->clientObtainer = $this->mock('Akamon\OAuth2\Server\Service\Client\ClientObtainer\ClientObtainerInterface');
        $this->userIdObtainer = $this->mock('Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface');
        $this->scopeObtainer = $this->mock('Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainerInterface');

        $this->contextObtainer = new ContextObtainer($this->clientObtainer, $this->userIdObtainer, $this->scopeObtainer);
    }

    public function testGetContext()
    {
        $client = $this->createClient();
        $userId = 'foo';
        $scope = 'bar';
        $username = 'pablodip';

        $request = new Request();
        $getUsername = function () use ($username) { return $username; };

        $this->clientObtainer->shouldReceive('getClient')->once()->with($request)->andReturn($client);
        $this->userIdObtainer->shouldReceive('getUserId')->once()->with($username)->andReturn($userId);
        $this->scopeObtainer->shouldReceive('getScope')->once()->with($request)->andReturn($scope);

        $context = $this->contextObtainer->getContext($request, $getUsername);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Model\Context', $context);
        $this->assertSame($client, $context->getClient());
        $this->assertSame($userId, $context->getUserId());
        $this->assertSame($scope, $context->getScope());
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException
     */
    public function testGetContextShouldThrowAnExceptionIfTheUserDoesNotExist()
    {
        $client = $this->createClient();
        $userId = 'foo';
        $scope = 'bar';
        $username = 'pablodip';

        $request = new Request();
        $getUsername = function () use ($username) { return $username; };

        $this->clientObtainer->shouldReceive('getClient')->andReturn($client);
        $this->userIdObtainer->shouldReceive('getUserId')->once()->with($username)->andThrow('Akamon\OAuth2\Server\Exception\UserNotFoundException');
        $this->scopeObtainer->shouldReceive('getScope')->andReturn($scope);

        $this->contextObtainer->getContext($request, $getUsername);
    }
}
