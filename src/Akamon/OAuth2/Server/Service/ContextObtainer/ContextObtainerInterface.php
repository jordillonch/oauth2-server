<?php

namespace Akamon\OAuth2\Server\Service\ContextObtainer;

use Symfony\Component\HttpFoundation\Request;
use Akamon\OAuth2\Server\Model\Context;

interface ContextObtainerInterface
{
    /**
     * @return Context
     */
    function getContext(Request $request, callable $getUsername);
}
