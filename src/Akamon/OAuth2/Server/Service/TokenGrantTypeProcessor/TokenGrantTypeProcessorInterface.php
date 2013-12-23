<?php

namespace Akamon\OAuth2\Server\Service\TokenGrantTypeProcessor;

use Symfony\Component\HttpFoundation\Request;

interface TokenGrantTypeProcessorInterface
{
    function getGrantType();

    function process(Request $request);
}
