<?php

namespace Akamon\OAuth2\Server\Domain\Tests;

use Akamon\OAuth2\Server\Domain\Storage;

class StorageTest extends OAuth2TestCase
{
    public function testStorage()
    {
        $clientRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface');
        $accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface');
        $refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface');
        $scopeRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface');

        $storage = new Storage($clientRepository, $accessTokenRepository, $refreshTokenRepository, $scopeRepository);

        $this->assertSame($clientRepository, $storage->getClientRepository());
        $this->assertSame($accessTokenRepository, $storage->getAccessTokenRepository());
        $this->assertSame($refreshTokenRepository, $storage->getRefreshTokenRepository());
        $this->assertSame($scopeRepository, $storage->getScopeRepository());
    }
}
