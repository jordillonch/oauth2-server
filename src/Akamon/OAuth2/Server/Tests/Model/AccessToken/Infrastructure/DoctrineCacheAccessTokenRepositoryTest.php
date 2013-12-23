<?php

namespace Akamon\OAuth2\Server\Tests\Model\AccessToken\Infrastructure;

use Akamon\OAuth2\Server\Model\AccessToken\Infrastructure\DoctrineCacheAccessTokenRepository;
use Akamon\OAuth2\Server\Tests\Model\AccessToken\AccessTokenRepositoryTestCase;
use Doctrine\Common\Cache\FilesystemCache;
use Mockery\MockInterface;
use felpado as f;

class DoctrineCacheAccessTokenRepositoryTest extends AccessTokenRepositoryTestCase
{
    /** @var MockInterface */
    private $cache;
    /** @var DoctrineCacheAccessTokenRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = $this->mock('Doctrine\Common\Cache\Cache');
        $this->repository = new DoctrineCacheAccessTokenRepository($this->cache);
    }

    public function testAdd()
    {
        $accessToken = $this->createAccessToken();
        $this->cache->shouldReceive('save')->once()->with(f\get($accessToken, 'token'), $accessToken->getParams(), $accessToken->getLifetime());

        $this->repository->add($accessToken);
    }

    public function testRemove()
    {
        $accessToken = $this->createAccessToken();
        $this->cache->shouldReceive('delete')->once()->with(f\get($accessToken, 'token'));

        $this->repository->remove($accessToken);
    }

    public function testFind()
    {
        $accessToken = $this->createAccessToken();
        $this->cache->shouldReceive('fetch')->once()->with(f\get($accessToken, 'token'))->andReturn($accessToken->getParams());

        $this->assertEquals($accessToken, $this->repository->find(f\get($accessToken, 'token')));
    }

    protected function createRepository()
    {
        $directory = sys_get_temp_dir().'/'.md5(microtime().mt_rand());
        $cache = new FilesystemCache($directory);

        return new DoctrineCacheAccessTokenRepository($cache);
    }
}
