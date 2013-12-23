<?php

namespace Akamon\OAuth2\Server;

use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface;

class Storage
{
    private $clientRepository;
    private $accessTokenRepository;
    private $refreshTokenRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, AccessTokenRepositoryInterface $accessTokenRepository, RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function getClientRepository()
    {
        return $this->clientRepository;
    }

    public function getAccessTokenRepository()
    {
        return $this->accessTokenRepository;
    }

    public function getRefreshTokenRepository()
    {
        return $this->refreshTokenRepository;
    }
}
