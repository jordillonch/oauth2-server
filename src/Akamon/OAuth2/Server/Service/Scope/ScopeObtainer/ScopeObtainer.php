<?php

namespace Akamon\OAuth2\Server\Service\Scope\ScopeObtainer;

use Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ScopeObtainer implements ScopeObtainerInterface
{
    public function getScope(Request $request)
    {
        return $request->request->get('scope');
    }
}
