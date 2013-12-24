<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGranter;

use Symfony\Component\HttpFoundation\Request;

interface TokenGranterInterface
{
    /**
     * @return array;
     */
    function grant(Request $request);
}
