<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\RefreshTokenCreator;

use Akamon\OAuth2\Server\Service\Token\RefreshTokenCreator\PersistentRefreshTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class PersistentRefreshTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $delegate;
    /** @var MockInterface */
    private $repository;

    /** @var PersistentRefreshTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->delegate = $this->mock('Akamon\OAuth2\Server\Service\Token\RefreshTokenCreator\RefreshTokenCreatorInterface');
        $this->repository = $this->mock('Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface');

        $this->creator = new PersistentRefreshTokenCreator($this->delegate, $this->repository);
    }

    public function testCreate()
    {
        $accessToken = $this->createAccessToken();
        $refreshToken = $this->createRefreshToken();

        $this->delegate->shouldReceive('create')->with($accessToken)->once()->andReturn($refreshToken)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($refreshToken, 'token'))->once()->andReturnNull()->globally()->ordered();
        $this->repository->shouldReceive('add')->with($refreshToken)->once()->globally()->ordered();

        $this->assertSame($refreshToken, $this->creator->create($accessToken));
    }

    public function testShouldCheckUniqueness()
    {
        $accessToken = $this->createAccessToken();
        $refreshToken1 = $this->createRefreshToken();
        $refreshToken2 = $this->createRefreshToken();

        $this->delegate->shouldReceive('create')->with($accessToken)->once()->andReturn($refreshToken1)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($refreshToken1, 'token'))->once()->andReturn($refreshToken1)->globally()->ordered();
        $this->delegate->shouldReceive('create')->with($accessToken)->once()->andReturn($refreshToken2)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($refreshToken2, 'token'))->once()->andReturnNull()->globally()->ordered();
        $this->repository->shouldReceive('add')->once()->with($refreshToken2)->globally()->ordered();

        $this->assertSame($refreshToken2, $this->creator->create($accessToken));
    }
}
