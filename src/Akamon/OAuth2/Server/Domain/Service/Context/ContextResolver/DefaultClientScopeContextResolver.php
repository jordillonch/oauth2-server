<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use felpado as f;

class DefaultClientScopeContextResolver implements ContextResolverInterface
{
    public function resolve(Context $context)
    {
        if (f\not($context->getScopes()->isEmpty())) {
            return $context;
        }

        $defaultScopes = ScopeCollection::createFromString(f\get($context->getClient(), 'defaultScope'));

        return new Context($context->getClient(), $context->getUserId(), $defaultScopes);
    }
}
