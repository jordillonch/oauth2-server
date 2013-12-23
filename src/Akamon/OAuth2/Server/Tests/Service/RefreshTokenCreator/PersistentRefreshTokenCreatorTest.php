<?php

namespace Akamon\OAuth2\Server\Tests\Service\RefreshTokenCreator;

use Akamon\OAuth2\Server\Service\RefreshTokenCreator\PersistentRefreshTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;

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
        $this->delegate = $this->mock('Akamon\OAuth2\Server\Service\RefreshTokenCreator\RefreshTokenCreatorInterface');
        $this->repository = $this->mock('Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface');

        $this->creator = new PersistentRefreshTokenCreator($this->delegate, $this->repository);
    }

    public function testCreate()
    {
        $accessToken = $this->createAccessToken();
        $refreshToken = $this->createRefreshToken();

        $this->delegate->shouldReceive('create')->once()->with($accessToken)->andReturn($refreshToken);
        $this->repository->shouldReceive('add')->once()->with($refreshToken);

        $this->assertSame($refreshToken, $this->creator->create($accessToken));
    }
}
