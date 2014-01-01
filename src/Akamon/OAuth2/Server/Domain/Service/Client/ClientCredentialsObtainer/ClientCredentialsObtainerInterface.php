<?php

namespace Akamon\OAuth2\Server\Domain\Service\Client\ClientCredentialsObtainer;

use Akamon\OAuth2\Server\Domain\Model\Client\ClientCredentials;
use Symfony\Component\HttpFoundation\Request;

interface ClientCredentialsObtainerInterface
{
    /**
     * @return ClientCredentials;
     */
    function getClientCredentials(Request $request);
}
