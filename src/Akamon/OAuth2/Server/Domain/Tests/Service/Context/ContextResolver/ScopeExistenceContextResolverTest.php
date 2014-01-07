<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ScopeExistenceContextResolver;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;

class ScopeExistenceContextResolverTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $repository;

    /** @var ScopeExistenceContextResolver */
    private $resolver;

    protected function setUp()
    {
        $this->repository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface');

        $this->resolver = new ScopeExistenceContextResolver($this->repository);
    }

    public function testItReturnsTheScopeWhenExists()
    {
        $client = $this->createClient();
        $userId = '2';
        $scope = 'foo';
        $context = new Context($client, $userId, $scope);

        $this->repository->shouldReceive('find')->with($scope)->once()->andReturn(new Scope($scope));

        $this->assertSame($context, $this->resolver->resolve($context));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheScopeDoesNotExist()
    {
        $client = $this->createClient();
        $userId = '2';
        $scope = 'foo';
        $context = new Context($client, $userId, $scope);

        $this->repository->shouldReceive('find')->with($scope)->once()->andReturnNull();

        $this->resolver->resolve($context);
    }
}
