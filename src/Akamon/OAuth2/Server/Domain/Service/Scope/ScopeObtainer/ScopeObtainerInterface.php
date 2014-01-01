<?php

namespace Akamon\OAuth2\Server\Domain\Service\Scope\ScopeObtainer;

interface ScopeObtainerInterface
{
    function getScope(array $inputData);
}
