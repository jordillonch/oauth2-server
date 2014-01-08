<?php

namespace Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use felpado as f;

class ScopesObtainer implements ScopesObtainerInterface
{
    public function getScopes(array $inputData)
    {
        $string = f\get_or($inputData, 'scope', null);

        return ScopeCollection::createFromString($string);
    }
}
