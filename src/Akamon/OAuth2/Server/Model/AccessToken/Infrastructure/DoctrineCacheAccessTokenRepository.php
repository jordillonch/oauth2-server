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
        $key = f\get($accessToken, 'token');
        $value = $accessToken->getParams();
        $lifetime = f\get($accessToken, 'lifetime');

        return $this->cache->save($key, $value, $lifetime);
    }

    public function remove(AccessToken $accessToken)
    {
        $key = f\get($accessToken, 'token');

        return $this->cache->delete($key);
    }

    public function find($token)
    {
        $params = $this->cache->fetch($token);

        return $params ? new AccessToken($params): null;
    }
}
