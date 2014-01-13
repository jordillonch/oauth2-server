<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\UnauthorizedClientForScopeOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\Context;
use felpado as f;

class ScopeAllowedContextResolver implements ContextResolverInterface
{
    public function resolve(Context $context)
    {
        $isScopeAllowed = [$context->getClient(), 'hasAllowedScope'];
        $scopeNames = $context->getScopes()->getNames();

        if (f\some(f\not_fn($isScopeAllowed), $scopeNames)) {
            throw new UnauthorizedClientForScopeOAuthErrorException();
        }

        return $context;
    }
}
