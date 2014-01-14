<?php

namespace Akamon\OAuth2\Server\Domain\Tests;

use Akamon\OAuth2\Server\Domain\OAuth2ServerBuilder;
use Akamon\OAuth2\Server\Domain\Storage;

class OAuth2ServerBuilderTest extends OAuth2TestCase
{
    private $storage;
    private $params;

    /** @var OAuth2ServerBuilder */
    private $builder;

    protected function setUp()
    {
        $this->storage = $this->createFullStorage();

        $lifetime = 3600;
        $resourceProcessor = function () { };
        $this->params = ['lifetime' => $lifetime, 'resource_processor' => $resourceProcessor];

        $this->builder = new OAuth2ServerBuilder($this->storage, $this->params);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testGetScopesObtainer(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $scopesObtainer = $builder->getScopesObtainer();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface', $scopesObtainer);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testGetTokenCreator(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $tokenCreator = $builder->getTokenCreator();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface', $tokenCreator);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testAddTokenGrantTypeProcessor(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $foo = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');
        $bar = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');

        $builder->addTokenGrantTypeProcessor('foo', $foo);
        $this->assertSame(['foo' => $foo], $builder->getTokenGrantTypeProcessors());

        $builder->addTokenGrantTypeProcessor('bar', $bar);
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $builder->getTokenGrantTypeProcessors());
    }

    /**
     * @dataProvider provideStorageParams
     * @expectedException \InvalidArgumentException
     */
    public function testAddTokenGrantTypeProcessorThrowsAnExceptionWhenTheProcessorAlreadyExists(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $processor1 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');
        $processor2 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface');

        $builder->addTokenGrantTypeProcessor('foo', $processor1);
        $builder->addTokenGrantTypeProcessor('foo', $processor2);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testBuildBasic(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $server = $builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testBuildWithResourceOwnerPasswordCredentialsGrantType(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $userCredentialsChecker = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface');
        $userIdObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\UserIdObtainerInterface');

        $builder->addResourceOwnerPasswordCredentialsGrantType($userCredentialsChecker, $userIdObtainer);

        $server = $builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    public function testBuildWithRefreshTokenGrantType()
    {
        $builder = new OAuth2ServerBuilder($this->createFullStorage(), $this->createParams());

        $builder->addRefreshTokenGrantType();

        $server = $builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    /**
     * @dataProvider provideStorageParams
     */
    public function testBuildWithPasswordFlow(Storage $storage, $params)
    {
        $builder = new OAuth2ServerBuilder($storage, $params);

        $server = $builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\OAuth2Server', $server);
    }

    public function provideStorageParams()
    {
        $params = $this->createParams();

        return [
            [$this->createFullStorage(), $params],
            [$this->createStorageWithoutRefreshTokenRepository(), $params]
        ];
    }

    private function createParams()
    {
        $lifetime = 3600;
        $resourceProcessor = function () { };

        return ['lifetime' => $lifetime, 'resource_processor' => $resourceProcessor];
    }

    private function createFullStorage()
    {
        $refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface');

        return $this->createStorage($refreshTokenRepository);
    }

    private function createStorageWithoutRefreshTokenRepository()
    {
        $refreshTokenRepository = null;

        return $this->createStorage($refreshTokenRepository);
    }

    private function createStorage($refreshTokenRepository)
    {
        $clientRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface');
        $accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface');

        $scopeRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface');

        return new Storage($clientRepository, $accessTokenRepository, $scopeRepository, $refreshTokenRepository);
    }
}
