<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor;

use Symfony\Component\HttpFoundation\Request;

interface TokenGrantTypeProcessorInterface
{
    function getGrantType();

    function process(Request $request);
}
