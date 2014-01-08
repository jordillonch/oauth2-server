<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ContextResolverInterface;
use Akamon\OAuth2\Server\Domain\Service\Scope\ScopeResolver\ScopeResolverInterface;

class ContextResolvedTokenCreator implements TokenCreatorInterface
{
    private $delegate;
    private $resolver;

    public function __construct(TokenCreatorInterface $delegate, ContextResolverInterface $resolver)
    {
        $this->delegate = $delegate;
        $this->resolver = $resolver;
    }

    /**
     * @return array An array of parameters.
     */
    public function create(Context $context)
    {
        return $this->delegate->create($this->resolver->resolve($context));
    }
}
