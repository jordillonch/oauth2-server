<?php

namespace Akamon\OAuth2\Server\Service\Scope\ScopeObtainer;

use felpado as f;

class ScopeObtainer implements ScopeObtainerInterface
{
    public function getScope(array $inputData)
    {
        return f\get_or($inputData, 'scope', null);
    }
}
