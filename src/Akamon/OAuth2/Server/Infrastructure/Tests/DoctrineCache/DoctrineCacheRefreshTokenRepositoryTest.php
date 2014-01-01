<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\DoctrineCache;

use Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheRefreshTokenRepository;
use Akamon\OAuth2\Server\Domain\Tests\Model\RefreshToken\RefreshTokenRepositoryTestCase;
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
        $this->repository = new DoctrineCacheRefreshTokenRepository($this->cache);
    }

    public function testAdd()
    {
        $refreshToken = $this->createRefreshToken();
        $this->cache
            ->shouldReceive('save')
            ->once()
            ->with(f\get($refreshToken, 'token'), $refreshToken->getParams(), f\get($refreshToken, 'lifetime'))
            ->andReturn(true);

        $this->assertTrue($this->repository->add($refreshToken));
    }

    public function testRemove()
    {
        $refreshToken = $this->createRefreshToken();
        $this->cache->shouldReceive('delete')->once()->with(f\get($refreshToken, 'token'))->andReturn(true);

        $this->assertTrue($this->repository->remove($refreshToken));
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
