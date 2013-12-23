<?php

namespace Akamon\OAuth2\Server\Tests\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Service\AccessTokenCreator\PersistentAccessTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;

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
        $this->creator = $this->mock('Akamon\OAuth2\Server\Service\AccessTokenCreator\AccessTokenCreatorInterface');
        $this->repository = $this->mock('Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface');

        $this->persistentCreator = new PersistentAccessTokenCreator($this->creator, $this->repository);
    }

    public function testCreateShouldPersistTheTokenAndReturnIt()
    {
        $context = $this->createContextMock();
        $accessToken = $this->createAccessToken();

        $this->creator->shouldReceive('create')->once()->with($context)->andReturn($accessToken);
        $this->repository->shouldReceive('add')->once()->with($accessToken);

        $this->assertSame($accessToken, $this->persistentCreator->create($context));
    }
}
