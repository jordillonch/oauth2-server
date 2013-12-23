<?php

namespace Akamon\OAuth2\Server\Model\AccessToken\Infrastructure;

use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Doctrine\Common\Cache\Cache;
use felpado as f;

class DoctrineCacheAccessTokenRepository implements AccessTokenRepositoryInterface
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function add(AccessToken $accessToken)
    {
        $this->cache->save(f\get($accessToken, 'token'), $accessToken->getParams(), $accessToken->getLifetime());
    }

    public function remove(AccessToken $accessToken)
    {
        $this->cache->delete(f\get($accessToken, 'token'));
    }

    public function find($token)
    {
        $params = $this->cache->fetch($token);

        return $params ? new AccessToken($params): null;
    }
}
