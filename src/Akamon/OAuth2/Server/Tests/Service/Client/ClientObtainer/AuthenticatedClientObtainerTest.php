<?php

namespace Akamon\OAuth2\Server\Tests\Service\Client\ClientObtainer;

use Akamon\OAuth2\Server\Model\Client\ClientCredentials;
use Akamon\OAuth2\Server\Service\Client\ClientObtainer\AuthenticatedClientObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticatedClientObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $credentialsObtainer;
    /** @var MockInterface */
    private $repository;

    /** @var AuthenticatedClientObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->credentialsObtainer = $this->mock('Akamon\OAuth2\Server\Service\Client\ClientCredentialsObtainer\ClientCredentialsObtainerInterface');
        $this->repository = $this->mock('Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface');

        $this->obtainer = new AuthenticatedClientObtainer($this->credentialsObtainer, $this->repository);
    }

    public function testGetClientOk()
    {
        $clientCredentials = new ClientCredentials('id', 'secret');
        $client = $this->createClient(['secret' => 'secret']);

        $request = new Request();

        $this->credentialsObtainer->shouldReceive('getClientCredentials')->once()->with($request)->andReturn($clientCredentials);
        $this->repository->shouldReceive('find')->once()->with($clientCredentials->getId())->andReturn($client);

        $this->assertSame($client, $this->obtainer->getClient($request));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException
     */
    public function testGetClientShouldThrowAnExceptionIfTheClientDoesNotExist()
    {
        $clientCredentials = new ClientCredentials('id', 'secret');

        $request = new Request();

        $this->credentialsObtainer->shouldReceive('getClientCredentials')->once()->with($request)->andReturn($clientCredentials);
        $this->repository->shouldReceive('find')->once()->with($clientCredentials->getId())->andReturn(null);

        $this->obtainer->getClient($request);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException
     */
    public function testGetClientShouldThrowAnExceptionIfTheClientSecretIsNotValid()
    {
        $clientCredentials = new ClientCredentials('id', 'secret');
        $client = $this->createClient(['secret' => 'no']);

        $request = new Request();

        $this->credentialsObtainer->shouldReceive('getClientCredentials')->once()->with($request)->andReturn($clientCredentials);
        $this->repository->shouldReceive('find')->once()->with($clientCredentials->getId())->andReturn($client);

        $this->obtainer->getClient($request);
    }
}
