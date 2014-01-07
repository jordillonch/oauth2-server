<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
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
        $scopeFoo = new Scope('foo');
        $scopes = new ScopeCollection([$scopeFoo]);
        $context = new Context($client, $userId, $scopes);

        $this->repository->shouldReceive('find')->with('foo')->once()->andReturn($scopeFoo);

        $this->assertSame($context, $this->resolver->resolve($context));
    }

    public function testItReturnsTheScopeWhenExistsWithSeveral()
    {
        $client = $this->createClient();
        $userId = '2';
        $scopeFoo = new Scope('foo');
        $scopeBar = new Scope('bar');
        $scopes = new ScopeCollection([$scopeFoo, $scopeBar]);
        $context = new Context($client, $userId, $scopes);

        $this->repository->shouldReceive('find')->with('foo')->once()->andReturn($scopeFoo)->ordered();
        $this->repository->shouldReceive('find')->with('bar')->once()->andReturn($scopeBar)->ordered();

        $this->assertSame($context, $this->resolver->resolve($context));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheScopeDoesNotExist()
    {
        $client = $this->createClient();
        $userId = '2';
        $scopes = new ScopeCollection([new Scope('foo')]);
        $context = new Context($client, $userId, $scopes);

        $this->repository->shouldReceive('find')->with('foo')->once()->andReturnNull();

        $this->resolver->resolve($context);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheScopeDoesNotExistWithSeveralFirst()
    {
        $client = $this->createClient();
        $userId = '2';
        $scopes = new ScopeCollection([new Scope('foo'), new Scope('bar')]);
        $context = new Context($client, $userId, $scopes);

        $this->repository->shouldReceive('find')->with('foo')->once()->andReturnNull();

        $this->resolver->resolve($context);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheScopeDoesNotExistWithSeveralNotFirst()
    {
        $client = $this->createClient();
        $userId = '2';
        $scopeFoo = new Scope('foo');
        $scopes = new ScopeCollection([$scopeFoo, new Scope('bar')]);
        $context = new Context($client, $userId, $scopes);

        $this->repository->shouldReceive('find')->with('foo')->once()->andReturn($scopeFoo)->ordered();
        $this->repository->shouldReceive('find')->with('bar')->once()->andReturnNull()->ordered();

        $this->resolver->resolve($context);
    }
}
