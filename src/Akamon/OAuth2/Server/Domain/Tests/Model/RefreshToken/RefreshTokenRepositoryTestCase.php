<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\RefreshToken;

use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface;
use felpado as f;

abstract class RefreshTokenRepositoryTestCase extends OAuth2TestCase
{
    /** @var RefreshTokenRepositoryInterface */
    private $repository;

    protected function setUp()
    {
        $this->repository = $this->createRepository();
    }

    abstract protected function createRepository();

    public function testAddShouldSaveAToken()
    {
        $token = $this->createRefreshToken();

        $this->repository->add($token);
        $this->assertEquals($token, $this->repository->find(f\get($token, 'token')));
    }

    public function testRemoveShouldRemoveAToken($value='')
    {
        $token1 = $this->createRefreshToken();
        $token2 = $this->createRefreshToken();

        $this->repository->add($token1);
        $this->repository->add($token2);

        $this->repository->remove($token2);

        $this->assertEquals($token1, $this->repository->find(f\get($token1, 'token')));
        $this->assertNull($this->repository->find(f\get($token2, 'token')));
    }
}
