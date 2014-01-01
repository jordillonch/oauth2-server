<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGranter;

use Symfony\Component\HttpFoundation\Request;

interface TokenGranterInterface
{
    /**
     * @return array;
     */
    function grant(Request $request);
}
