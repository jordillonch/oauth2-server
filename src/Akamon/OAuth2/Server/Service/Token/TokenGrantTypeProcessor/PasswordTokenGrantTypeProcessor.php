<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\UserCredentialsNotFoundException;
use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Model\User\UserCredentials;
use Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainerInterface;
use Akamon\OAuth2\Server\Service\Token\TokenCreator\TokenCreatorInterface;
use Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface;
use felpado as f;

class PasswordTokenGrantTypeProcessor implements TokenGrantTypeProcessorInterface
{
    const GRANT_TYPE = 'password';

    private $userCredentialsChecker;
    private $userIdObtainer;
    private $scopeObtainer;
    private $tokenCreator;

    public function __construct(
        UserCredentialsCheckerInterface $userCredentialsChecker,
        UserIdObtainerInterface $userIdObtainer,
        ScopeObtainerInterface $scopeObtainer,
        TokenCreatorInterface $tokenCreator
    ) {
        $this->userCredentialsChecker = $userCredentialsChecker;
        $this->userIdObtainer = $userIdObtainer;
        $this->scopeObtainer = $scopeObtainer;
        $this->tokenCreator = $tokenCreator;
    }

    public function getGrantType()
    {
        return self::GRANT_TYPE;
    }

    public function process(Client $client, array $inputData)
    {
        $userCredentials = $this->getUserCredentialsFromInputData($inputData);
        if (!$this->userCredentialsChecker->check($userCredentials)) {
            throw new InvalidUserCredentialsOAuthErrorException();
        }

        $userId = $this->userIdObtainer->getUserId($userCredentials->getUsername());
        $scope = $this->scopeObtainer->getScope($inputData);

        $context = new Context($client, $userId, $scope);

        return $this->tokenCreator->create($context);
    }

    private function getUserCredentialsFromInputData($inputData)
    {
        if ($this->hasNoUserCredentialsInInputData($inputData)) {
            throw new UserCredentialsNotFoundException();
        }

        $username = f\get($inputData, 'username');
        $password = f\get($inputData, 'password');

        return new UserCredentials($username, $password);
    }

    private function hasNoUserCredentialsInInputData($inputData)
    {
        return f\not(f\contains($inputData, 'username')) ||
               f\not(f\contains($inputData, 'password'));
    }
}
