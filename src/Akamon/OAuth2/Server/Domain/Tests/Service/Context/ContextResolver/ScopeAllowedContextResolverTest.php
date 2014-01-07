<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ScopeAllowedContextResolver;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ScopeAllowedContextResolverTest extends OAuth2TestCase
{
    public function testItReturnsTheSameContextIfTheScopeIsAllowedForTheClient()
    {
        $client = $this->createClient(['allowedScopes' => ['foo']]);
        $userId = 1;
        $scope = 'foo';
        $context = new Context($client, $userId, $scope);

        $resolver = new ScopeAllowedContextResolver();

        $this->assertSame($context, $resolver->resolve($context));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\UnauthorizedClientForScopeOAuthErrorException
     */
    public function testItThrowsAnExceptiontIfTheScopeIsAllowedForTheClient()
    {
        $client = $this->createClient();
        $userId = 1;
        $scope = 'foo';
        $context = new Context($client, $userId, $scope);

        $resolver = new ScopeAllowedContextResolver();

        $resolver->resolve($context);
    }
}
