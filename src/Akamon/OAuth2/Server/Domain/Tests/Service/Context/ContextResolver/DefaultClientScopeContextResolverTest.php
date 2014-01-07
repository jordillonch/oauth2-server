<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\DefaultClientScopeContextResolver;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class DefaultClientScopeContextResolverTest extends OAuth2TestCase
{
    public function testItReturnsTheSameContextIfTheContextHasAnyScope()
    {
        $client = $this->createClient();
        $userId = '1';
        $scopes = new ScopeCollection([new Scope('foo')]);

        $context = new Context($client, $userId, $scopes);
        $resolver = new DefaultClientScopeContextResolver();

        $this->assertSame($context, $resolver->resolve($context));
    }

    public function testItSetsTheDefaultClientScopeIfTheAreNoScopes()
    {
        $client = $this->createClient(['defaultScope' => 'foo bar']);
        $userId = '1';
        $scopes = new ScopeCollection([]);

        $context = new Context($client, $userId, $scopes);
        $contextWithDefaultScope = new Context($client, $userId, new ScopeCollection([new Scope('foo'), new Scope('bar')]));

        $resolver = new DefaultClientScopeContextResolver();

        $this->assertEquals($contextWithDefaultScope, $resolver->resolve($context));
    }
}
