<?php

namespace Akamon\OAuth2\Server\Service\ClientCredentialsObtainer;

use Akamon\OAuth2\Server\Model\Client\ClientCredentials;
use Symfony\Component\HttpFoundation\Request;

interface ClientCredentialsObtainerInterface
{
    /**
     * @return ClientCredentials;
     */
    function getClientCredentials(Request $request);
}
