<?php

namespace Akamon\OAuth2\Server\Domain\Service\Client\ClientObtainer;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Service\Client\ClientCredentialsObtainer\ClientCredentialsObtainerInterface;
use Akamon\OAuth2\Server\Domain\Service\Client\ClientObtainer\ClientObtainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticatedClientObtainer implements ClientObtainerInterface
{
    private $credentialsObtainer;
    private $repository;

    public function __construct(ClientCredentialsObtainerInterface $credentialsObtainer, ClientRepositoryInterface $repository)
    {
        $this->credentialsObtainer = $credentialsObtainer;
        $this->repository = $repository;
    }

    /**
     * @return Client
     */
    public function getClient(Request $request)
    {
        $credentials = $this->credentialsObtainer->getClientCredentials($request);
        $client = $this->repository->find($credentials->getId());

        if (is_null($client) || !($client->checkSecret($credentials->getSecret()))) {
            throw new InvalidClientCredentialsOAuthErrorException();
        }

        return $client;
    }
}
