<?php

namespace Akamon\OAuth2\Server\Service\Client\ClientObtainer;

use Akamon\OAuth2\Server\Model\Client\Client;
use Symfony\Component\HttpFoundation\Request;

interface ClientObtainerInterface
{
    /**
     * @return Client
     */
    function getClient(Request $request);
}
