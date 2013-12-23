<?php

namespace Akamon\OAuth2\Server\Service\ClientObtainer;

use Akamon\OAuth2\Server\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Service\ClientCredentialsObtainer\ClientCredentialsObtainerInterface;
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
