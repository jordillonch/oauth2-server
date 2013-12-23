<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\Behat\ApiContext\Client\ClientInterface;
use Akamon\OAuth2\Server\OAuth2Server;
use Symfony\Component\HttpFoundation as Http;

class OAuth2Client implements ClientInterface
{
    private $server;

    public function __construct(OAuth2Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Http\Response
     */
    public function request(Http\Request $request)
    {
        if ($request->getPathInfo() === '/oauth/token') {
            return $this->server->token($request);
        }

        throw new \Exception('Invalid request.');
    }
}
