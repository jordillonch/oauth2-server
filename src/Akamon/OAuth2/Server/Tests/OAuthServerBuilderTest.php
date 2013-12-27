<?php

namespace Akamon\OAuth2\Server\Tests;

use Akamon\OAuth2\Server\OAuth2ServerBuilder;
use Akamon\OAuth2\Server\Storage;

class OAuth2ServerBuilderTest extends OAuth2TestCase
{
    private $storage;

    /** @var OAuth2ServerBuilder */
    private $builder;

    protected function setUp()
    {
        $this->storage = $this->buildStorage();

        $this->builder = new OAuth2ServerBuilder($this->storage);
    }

    private function buildStorage()
    {
        $clientRepository = $this->mock('Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface');
        $accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface');
        $refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface');

        return new Storage($clientRepository, $accessTokenRepository, $refreshTokenRepository);
    }

    public function testBuildBasic()
    {
        $server = $this->builder->build();

        $this->assertInstanceOf('Akamon\OAuth2\Server\OAuth2Server', $server);
    }

    public function testBuildWithResourceOwnerPasswordCredentialsGrant()
    {
        $userCredentialsChecker = $this->mock('Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface');
        $userIdObtainer = $this->mock('Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface');

        $this->builder->addResourceOwnerPasswordCredentialsGrant($userCredentialsChecker, $userIdObtainer);

        $server = $this->builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\OAuth2Server', $server);
    }

    public function testBuildWithPasswordFlow()
    {
        $server = $this->builder->build();
        $this->assertInstanceOf('Akamon\OAuth2\Server\OAuth2Server', $server);
    }
}
