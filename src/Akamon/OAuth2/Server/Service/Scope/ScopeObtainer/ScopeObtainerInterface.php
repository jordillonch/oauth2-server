<?php

namespace Akamon\OAuth2\Server\Service\Scope\ScopeObtainer;

interface ScopeObtainerInterface
{
    function getScope(array $inputData);
}
