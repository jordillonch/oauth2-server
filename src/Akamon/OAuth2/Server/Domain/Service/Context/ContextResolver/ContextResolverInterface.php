<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Model\Context;

interface ContextResolverInterface
{
    function resolve(Context $context);
}
