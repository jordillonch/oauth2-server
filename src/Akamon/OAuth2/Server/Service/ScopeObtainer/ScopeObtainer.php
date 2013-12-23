<?php

namespace Akamon\OAuth2\Server\Service\ScopeObtainer;

use Symfony\Component\HttpFoundation\Request;

class ScopeObtainer implements ScopeObtainerInterface
{
    public function getScope(Request $request)
    {
        return $request->request->get('scope');
    }
}
