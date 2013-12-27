<?php

namespace Akamon\OAuth2\Server;

use Akamon\OAuth2\Server\Controller\TokenController;
use Akamon\OAuth2\Server\Service\Token\AccessTokenCreator\AccessTokenCreator;
use Akamon\OAuth2\Server\Service\Token\AccessTokenCreator\PersistentAccessTokenCreator;
use Akamon\OAuth2\Server\Service\Client\ClientCredentialsObtainer\HttpBasicClientCredentialsObtainer;
use Akamon\OAuth2\Server\Service\Client\ClientObtainer\AuthenticatedClientObtainer;
use Akamon\OAuth2\Server\Service\Token\RandomGenerator\ArrayRandRandomGenerator;
use Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Service\Token\TokenCreator\TokenCreator;
use Akamon\OAuth2\Server\Service\Token\TokenGenerator\BearerTokenGenerator;
use Akamon\OAuth2\Server\Service\Token\TokenGranter\TokenGranterByGrantType;
use Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface;
use Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface;

class OAuth2ServerBuilder
{
    private $storage;

    private $scopeObtainer;
    private $tokenGenerator;

    private $clientObtainer;
    private $tokenCreator;

    private $tokenGrantTypeProcessors = [];

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;

        $this->scopeObtainer = new ScopeObtainer();
        $this->tokenGenerator = new BearerTokenGenerator(new ArrayRandRandomGenerator());

        $this->clientObtainer = $this->createClientObtainer();
        $this->tokenCreator = $this->createTokenCreator();
    }

    private function createClientObtainer()
    {
        $clientCredentialsObtainer = new HttpBasicClientCredentialsObtainer();

        return new AuthenticatedClientObtainer($clientCredentialsObtainer, $this->storage->getClientRepository());
    }

    private function createTokenCreator()
    {
        $accessTokenCreator = $this->createAccessTokenCreator();

        return new TokenCreator($accessTokenCreator);
    }

    private function createAccessTokenCreator()
    {
        $params = ['type' => 'bearer', 'lifetime' => 3600];
        $creator = new AccessTokenCreator($this->tokenGenerator, $params);

        $accessTokenRepository = $this->storage->getAccessTokenRepository();

        return new PersistentAccessTokenCreator($creator, $accessTokenRepository);
    }

    public function addResourceOwnerPasswordCredentialsGrant(UserCredentialsCheckerInterface $userCredentialsChecker, UserIdObtainerInterface $userIdObtainer)
    {
        $processor = new PasswordTokenGrantTypeProcessor($userCredentialsChecker, $userIdObtainer, $this->scopeObtainer, $this->tokenCreator);

        $this->addTokenGrantTypeProcessor($processor);
    }

    private function addTokenGrantTypeProcessor(TokenGrantTypeProcessorInterface $processor)
    {
        $this->tokenGrantTypeProcessors[] = $processor;
    }

    public function build()
    {
        $tokenController = $this->buildTokenController();

        return new OAuth2Server($tokenController);
    }

    private function buildTokenController()
    {
        $tokenGranter = new TokenGranterByGrantType($this->clientObtainer, $this->tokenGrantTypeProcessors);

        return new TokenController($tokenGranter);
    }
}
