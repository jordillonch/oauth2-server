<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\UnauthorizedClientForScopeOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\Context;
use felpado as f;

class ScopeAllowedContextResolver implements ContextResolverInterface
{
    public function resolve(Context $context)
    {
        if (f\not($context->getClient()->hasAllowedScope($context->getScope()))) {
            throw new UnauthorizedClientForScopeOAuthErrorException();
        }
        return $context;
    }
}
