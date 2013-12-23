<?php

namespace Akamon\OAuth2\Server\Tests;

use Akamon\OAuth2\Server\Storage;

class StorageTest extends OAuth2TestCase
{
    public function testStorage()
    {
        $clientRepository = $this->mock('Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface');
        $accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface');
        $refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface');

        $storage = new Storage($clientRepository, $accessTokenRepository, $refreshTokenRepository);

        $this->assertSame($clientRepository, $storage->getClientRepository());
        $this->assertSame($accessTokenRepository, $storage->getAccessTokenRepository());
        $this->assertSame($refreshTokenRepository, $storage->getRefreshTokenRepository());
    }
}
