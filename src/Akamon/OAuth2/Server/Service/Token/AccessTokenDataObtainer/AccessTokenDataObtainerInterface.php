<?php

namespace Akamon\OAuth2\Server\Service\Token\AccessTokenDataObtainer;

use Symfony\Component\HttpFoundation\Request;

interface AccessTokenDataObtainerInterface
{
    /**
     * @return array
     */
    function obtain(Request $request);
}
