<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\AccessTokenCreator;

use Akamon\OAuth2\Server\Service\Token\AccessTokenCreator\PersistentAccessTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class PersistentAccessTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $creator;
    /** @var MockInterface */
    private $repository;

    /** @var PersistentAccessTokenCreator */
    private $persistentCreator;

    protected function setUp()
    {
        $this->creator = $this->mock('Akamon\OAuth2\Server\Service\Token\AccessTokenCreator\AccessTokenCreatorInterface');
        $this->repository = $this->mock('Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface');

        $this->persistentCreator = new PersistentAccessTokenCreator($this->creator, $this->repository);
    }

    public function testCreateShouldPersistTheTokenAndReturnIt()
    {
        $context = $this->createContextMock();
        $accessToken = $this->createAccessToken();

        $this->creator->shouldReceive('create')->with($context)->once()->andReturn($accessToken)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($accessToken, 'token'))->once()->andReturnNull()->globally()->ordered();
        $this->repository->shouldReceive('add')->with($accessToken)->once()->globally()->ordered();

        $this->assertSame($accessToken, $this->persistentCreator->create($context));
    }

    public function testCreateShouldCheckUniqueness()
    {
        $context = $this->createContextMock();
        $accessToken1 = $this->createAccessToken();
        $accessToken2 = $this->createAccessToken();

        $this->creator->shouldReceive('create')->with($context)->once()->andReturn($accessToken1)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($accessToken1, 'token'))->once()->andReturn($accessToken1)->globally()->ordered();
        $this->creator->shouldReceive('create')->with($context)->once()->andReturn($accessToken2)->globally()->ordered();
        $this->repository->shouldReceive('find')->with(f\get($accessToken2, 'token'))->once()->andReturnNull()->globally()->ordered();
        $this->repository->shouldReceive('add')->with($accessToken2)->once();

        $this->assertSame($accessToken2, $this->persistentCreator->create($context));
    }
}
