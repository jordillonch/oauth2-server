<?php

namespace Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer;

interface ScopesObtainerInterface
{
    function getScopes(array $inputData);
}
