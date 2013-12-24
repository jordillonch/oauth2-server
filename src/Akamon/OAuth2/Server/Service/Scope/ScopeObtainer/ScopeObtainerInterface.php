<?php

namespace Akamon\OAuth2\Server\Service\Scope\ScopeObtainer;

use Symfony\Component\HttpFoundation\Request;

interface ScopeObtainerInterface
{
    function getScope(Request $request);
}
