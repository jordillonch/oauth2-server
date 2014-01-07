<?php

namespace Akamon\OAuth2\Server\Domain;

use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;

class Storage
{
    private $clientRepository;
    private $accessTokenRepository;
    private $refreshTokenRepository;
    private $scopeRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, AccessTokenRepositoryInterface $accessTokenRepository, RefreshTokenRepositoryInterface $refreshTokenRepository, ScopeRepositoryInterface $scopeRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->scopeRepository = $scopeRepository;
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

    public function getScopeRepository()
    {
        return $this->scopeRepository;
    }
}
