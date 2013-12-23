<?php

namespace Akamon\OAuth2\Server\Tests\Model\AccessToken;

use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use felpado as f;

abstract class AccessTokenRepositoryTestCase extends OAuth2TestCase
{
    /**
     * @var AccessTokenRepositoryInterface
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = $this->createRepository();
    }

    abstract protected function createRepository();

    public function testAddShouldSaveAToken()
    {
        $token = $this->createAccessToken();

        $this->repository->add($token);
        $this->assertEquals($token, $this->repository->find(f\get($token->getParams(), 'token')));
    }

    public function testRemoveShouldRemoveAToken()
    {
        $token1 = $this->createAccessToken();
        $token2 = $this->createAccessToken();

        $this->repository->add($token1);
        $this->repository->add($token2);

        $this->repository->remove($token2);

        $this->assertEquals($token1, $this->repository->find(f\get($token1->getParams(), 'token')));
        $this->assertNull($this->repository->find(f\get($token2->getParams(), 'token')));
    }
}
