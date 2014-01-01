<?php

namespace Akamon\OAuth2\Server\Domain\Service\Client\ClientObtainer;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Symfony\Component\HttpFoundation\Request;

interface ClientObtainerInterface
{
    /**
     * @return Client
     */
    function getClient(Request $request);
}
