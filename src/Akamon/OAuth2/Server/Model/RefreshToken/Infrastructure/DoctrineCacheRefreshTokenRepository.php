<?php

namespace Akamon\OAuth2\Server\Model\RefreshToken\Infrastructure;

use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface;
use Doctrine\Common\Cache\Cache;
use felpado as f;

class DoctrineCacheRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function add(RefreshToken $refreshToken)
    {
        $this->cache->save(f\get($refreshToken, 'token'), $refreshToken->getParams(), $refreshToken->getLifetime());
    }

    public function remove(RefreshToken $refreshToken)
    {
        $this->cache->delete(f\get($refreshToken, 'token'));
    }

    public function find($token)
    {
        $params = $this->cache->fetch($token);

        return $params ? new RefreshToken($params) : null;
    }
}
