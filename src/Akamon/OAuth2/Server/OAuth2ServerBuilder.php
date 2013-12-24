<?php

namespace Akamon\OAuth2\Server;

use Akamon\OAuth2\Server\Controller\TokenController;
use Akamon\OAuth2\Server\Service\AccessTokenCreator\AccessTokenCreator;
use Akamon\OAuth2\Server\Service\AccessTokenCreator\PersistentAccessTokenCreator;
use Akamon\OAuth2\Server\Service\ClientCredentialsObtainer\HttpBasicClientCredentialsObtainer;
use Akamon\OAuth2\Server\Service\ClientObtainer\AuthenticatedClientObtainer;
use Akamon\OAuth2\Server\Service\ContextObtainer\ContextObtainer;
use Akamon\OAuth2\Server\Service\RandomGenerator\ArrayRandRandomGenerator;
use Akamon\OAuth2\Server\Service\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Service\TokenCreator\TokenCreator;
use Akamon\OAuth2\Server\Service\TokenGenerator\BearerTokenGenerator;
use Akamon\OAuth2\Server\Service\TokenGranter\TokenGranterByGrantType;
use Akamon\OAuth2\Server\Service\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Service\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface;
use Akamon\OAuth2\Server\Service\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Akamon\OAuth2\Server\Service\UserIdObtainer\UserIdObtainerInterface;

class OAuth2ServerBuilder
{
    private $storage;
    private $userIdObtainer;

    private $scopeObtainer;
    private $tokenGenerator;

    private $clientObtainer;
    private $contextObtainer;
    private $tokenCreator;

    private $tokenGrantTypeProcessors = [];

    public function __construct(Storage $storage, UserIdObtainerInterface $userIdObtainer)
    {
        $this->storage = $storage;
        $this->userIdObtainer = $userIdObtainer;

        $this->scopeObtainer = new ScopeObtainer();
        $this->tokenGenerator = new BearerTokenGenerator(new ArrayRandRandomGenerator());

        $this->clientObtainer = $this->createClientObtainer();
        $this->contextObtainer = $this->createContextObtainer();
        $this->tokenCreator = $this->createTokenCreator();
    }

    private function createClientObtainer()
    {
        $clientCredentialsObtainer = new HttpBasicClientCredentialsObtainer();

        return new AuthenticatedClientObtainer($clientCredentialsObtainer, $this->storage->getClientRepository());
    }

    private function createContextObtainer()
    {
        return new ContextObtainer($this->clientObtainer, $this->userIdObtainer, $this->scopeObtainer);
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

    public function addResourceOwnerPasswordCredentialsGrant(UserCredentialsCheckerInterface $userCredentialsChecker)
    {
        $processor = new PasswordTokenGrantTypeProcessor($this->contextObtainer, $userCredentialsChecker, $this->tokenCreator);

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
