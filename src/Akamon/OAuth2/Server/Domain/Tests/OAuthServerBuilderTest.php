<?php

namespace Akamon\OAuth2\Server\Domain\Tests;

use Akamon\OAuth2\Server\Domain\OAuth2ServerBuilder;
use Akamon\OAuth2\Server\Domain\Storage;

class OAuth2ServerBuilderTest extends OAuth2TestCase
{
    private $storage;

    /** @var OAuth2ServerBuilder */
    private $builder;

    protected function setUp()
    {
        $this->storage = $this->buildStorage();

        $lifetime = 3600;
        $resourceProcessor = function () { };

        $this->builder = new OAuth2ServerBuilder($this->storage, ['lifetime' => $lifetime, 'resource_processor' => $resourceProcessor]);
    }

    private function buildStorage()
    {
        $clientRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface');
        $accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface');
        $refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface');
        $scopeRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface');

        return new Storage($clientRepository, $accessTokenRepository, $refreshTokenRepository, $scopeRepository);
    }

    public function testGetScopesObtainer()
    {
        $scopesObtainer = $this->builder->getScopesObtainer();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface', $scopesObtainer);
    }

    public function testGetTokenCreator()
    {
        $tokenCreator = $this->builder->getTokenCreator();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface', $tokenCreator);
    }

    public function testAddTokenGrantTypeProcessor()
    {
        $foo = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');
        $bar = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');

        $this->builder->addTokenGrantTypeProcessor('foo', $foo);
        $this->assertSame(['foo' => $foo], $this->builder->getTokenGrantTypeProcessors());

        $this->builder->addTokenGrantTypeProcessor('bar', $bar);
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $this->builder->getTokenGrantTypeProcessors());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddTokenGrantTypeProcessorThrowsAnExceptionWhenTheProcessorAlreadyExists()
    {
        $processor1 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');
        $processor2 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');

        $this->builder->addTokenGrantTypeProcessor('foo', $processor1);
        $this->builder->addTokenGrantTypeProcessor('foo', $processor2);
    }

    public function testBuildBasic()
    {
        $server = $this->builder->build();

        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    public function testBuildWithResourceOwnerPasswordCredentialsGrantType()
    {
        $userCredentialsChecker = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface');
        $userIdObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\UserIdObtainerInterface');

        $this->builder->addResourceOwnerPasswordCredentialsGrantType($userCredentialsChecker, $userIdObtainer);

        $server = $this->builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    public function testBuildWithRefreshTokenGrantType()
    {
        $this->builder->addRefreshTokenGrantType();

        $server = $this->builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    public function testBuildWithPasswordFlow()
    {
        $server = $this->builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }
}
