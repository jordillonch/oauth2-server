<?php

namespace Akamon\OAuth2\Server\Domain;

use Akamon\OAuth2\Server\Domain\Controller\ResourceController;
use Akamon\OAuth2\Server\Domain\Controller\TokenController;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ComposedContextResolver;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ScopeAllowedContextResolver;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ScopeExistenceContextResolver;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\AccessTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\PersistentAccessTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Client\ClientCredentialsObtainer\HttpBasicClientCredentialsObtainer;
use Akamon\OAuth2\Server\Domain\Service\Client\ClientObtainer\AuthenticatedClientObtainer;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenDataObtainer\BearerAccessTokenDataObtainer;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenObtainer\AccessTokenObtainer;
use Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator\ArrayRandRandomGenerator;
use Akamon\OAuth2\Server\Domain\Service\Scope\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Domain\Service\Token\RefreshTokenCreator\PersistentRefreshTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\RefreshTokenCreator\RefreshTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainer;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\RefreshingTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\ContextResolvedTokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreator;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGenerator\BearerTokenGenerator;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGranter\TokenGranterByGrantType;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\RefreshTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface;
use Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\UserIdObtainerInterface;
use felpado as f;

class OAuth2ServerBuilder
{
    private $storage;

    private $lifetime;
    private $resourceProcessor;

    private $scopeObtainer;
    private $tokenGenerator;

    private $clientObtainer;
    private $tokenCreator;

    private $tokenGrantTypeProcessors = [];

    public function __construct(Storage $storage, $params)
    {
        $this->storage = $storage;

        f\validate_collection_or_throw($params, [
            'lifetime' => f\required(['v' => 'is_int']),
            'resource_processor' => f\required(['v' => 'is_callable'])
        ]);
        $this->lifetime = $params['lifetime'];
        $this->resourceProcessor = $params['resource_processor'];

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
        $accessingCreator = new TokenCreator($accessTokenCreator);

        $refreshTokenCreator = $this->createRefreshTokenCreator();
        $refreshingCreator = new RefreshingTokenCreator($accessingCreator, $refreshTokenCreator);

        return new ContextResolvedTokenCreator($refreshingCreator, $this->createContextResolver());
    }

    private function createAccessTokenCreator()
    {
        $params = ['type' => 'bearer', 'lifetime' => $this->lifetime];
        $creator = new AccessTokenCreator($this->tokenGenerator, $params);

        $accessTokenRepository = $this->storage->getAccessTokenRepository();

        return new PersistentAccessTokenCreator($creator, $accessTokenRepository);
    }

    private function createRefreshTokenCreator()
    {
        $params = ['lifetime' => $this->lifetime];
        $creator = new RefreshTokenCreator($this->tokenGenerator, $params);

        $repository = $this->storage->getRefreshTokenRepository();

        return new PersistentRefreshTokenCreator($creator, $repository);
    }

    private function createContextResolver()
    {
        return new ComposedContextResolver([
            new ScopeExistenceContextResolver($this->storage->getScopeRepository()),
            new ScopeAllowedContextResolver()
        ]);
    }

    public function addResourceOwnerPasswordCredentialsGrantType(UserCredentialsCheckerInterface $userCredentialsChecker, UserIdObtainerInterface $userIdObtainer)
    {
        $processor = new PasswordTokenGrantTypeProcessor($userCredentialsChecker, $userIdObtainer, $this->scopeObtainer, $this->tokenCreator);

        $this->addTokenGrantTypeProcessor($processor);
    }

    public function addRefreshTokenGrantType()
    {
        $processor = new RefreshTokenGrantTypeProcessor($this->storage->getRefreshTokenRepository(), $this->storage->getAccessTokenRepository(), $this->tokenCreator);

        $this->addTokenGrantTypeProcessor($processor);
    }

    private function addTokenGrantTypeProcessor(TokenGrantTypeProcessorInterface $processor)
    {
        $this->tokenGrantTypeProcessors[] = $processor;
    }

    public function build()
    {
        $tokenController = $this->buildTokenController();
        $resourceController = $this->buildResourceController();

        return new OAuth2Server($tokenController, $resourceController);
    }

    private function buildTokenController()
    {
        $tokenGranter = new TokenGranterByGrantType($this->clientObtainer, $this->tokenGrantTypeProcessors);

        return new TokenController($tokenGranter);
    }

    private function buildResourceController()
    {
        $requestAccessTokenObtainer = $this->buildRequestAccessTokenObtainer();

        return new ResourceController($requestAccessTokenObtainer, $this->resourceProcessor);
    }

    private function buildRequestAccessTokenObtainer()
    {
        $accessTokenDataObtainer = new BearerAccessTokenDataObtainer();

        $accessTokenRepository = $this->storage->getAccessTokenRepository();
        $accessTokenObtainer = new AccessTokenObtainer($accessTokenRepository);

        return new RequestAccessTokenObtainer($accessTokenDataObtainer, $accessTokenObtainer);
    }
}
