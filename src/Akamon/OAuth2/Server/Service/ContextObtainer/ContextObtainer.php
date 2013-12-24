<?php

namespace Akamon\OAuth2\Server\Service\ContextObtainer;

use Akamon\OAuth2\Server\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\UserCredentialsNotFoundException;
use Akamon\OAuth2\Server\Exception\UserNotFoundException;
use Akamon\OAuth2\Server\Service\Client\ClientObtainer\ClientObtainerInterface;
use Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainerInterface;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface;
use Akamon\OAuth2\Server\Model\Context;
use Symfony\Component\HttpFoundation\Request;

class ContextObtainer implements ContextObtainerInterface
{
    private $clientObtainer;
    private $userIdObtainer;
    private $scopeObtainer;

    public function __construct(ClientObtainerInterface $clientObtainer, UserIdObtainerInterface $userIdObtainer, ScopeObtainerInterface $scopeObtainer)
    {
        $this->clientObtainer = $clientObtainer;
        $this->userIdObtainer = $userIdObtainer;
        $this->scopeObtainer = $scopeObtainer;
    }

    /**
     * @return Context
     */
    public function getContext(Request $request, callable $getUsername)
    {
        $client = $this->clientObtainer->getClient($request);
        $scope = $this->scopeObtainer->getScope($request);

        try {
            $userId = $this->userIdObtainer->getUserId(call_user_func($getUsername));
        } catch (UserNotFoundException $e) {
            throw new InvalidUserCredentialsOAuthErrorException();
        }

        return new Context($client, $userId, $scope);
    }
}
