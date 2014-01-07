<?php

namespace Akamon\OAuth2\Server\Domain\Tests;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
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
