<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;
use felpado as f;

class ComposedContextResolver implements ContextResolverInterface
{
    private $resolvers = [];

    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function resolve(Context $context)
    {
        $resolver = function ($r) { return [$r, 'resolve']; };
        $composed = call_user_func_array('felpado\compose', f\reverse(f\map($resolver, $this->resolvers)));

        return $composed($context);
    }
}
