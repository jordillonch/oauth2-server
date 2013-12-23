<?php

namespace Akamon\OAuth2\Server\Tests\Model\RefreshToken\Infrastructure;

use Akamon\OAuth2\Server\Model\RefreshToken\Infrastructure\DoctrineCacheRefreshTokenRepository;
use Akamon\OAuth2\Server\Tests\Model\RefreshToken\RefreshTokenRepositoryTestCase;
use Doctrine\Common\Cache\FilesystemCache;
use Mockery\MockInterface;
use felpado as f;

class DoctrineCacheRefreshTokenRepositoryTest extends RefreshTokenRepositoryTestCase
{
    /** @var MockInterface */
    private $cache;
    /** @var DoctrineCacheRefreshTokenRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = $this->mock('Doctrine\Common\Cache\Cache');
        $this->repository = new \Akamon\OAuth2\Server\Model\RefreshToken\Infrastructure\DoctrineCacheRefreshTokenRepository($this->cache);
    }

    public function testAdd()
    {
        $refreshToken = $this->createRefreshToken();
        $this->cache->shouldReceive('save')->once()->with(f\get($refreshToken, 'token'), $refreshToken->getParams(), $refreshToken->getLifetime());

        $this->repository->add($refreshToken);
    }

    public function testRemove()
    {
        $refreshToken = $this->createRefreshToken();
        $this->cache->shouldReceive('delete')->once()->with(f\get($refreshToken, 'token'));

        $this->repository->remove($refreshToken);
    }

    public function testFind()
    {
        $refreshToken = $this->createRefreshToken();
        $this->cache->shouldReceive('fetch')->once()->with(f\get($refreshToken, 'token'))->andReturn($refreshToken->getParams());

        $this->assertEquals($refreshToken, $this->repository->find(f\get($refreshToken, 'token')));
    }

    protected function createRepository()
    {
        $directory = sys_get_temp_dir().'/'.md5(microtime().mt_rand());
        $cache = new FilesystemCache($directory);

        return new DoctrineCacheRefreshTokenRepository($cache);
    }
}
