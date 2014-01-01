<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\Behat\ApiContext\Client\ClientInterface;
use Akamon\OAuth2\Server\Domain\OAuth2Server;
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
        $uri = $request->getPathInfo();

        if ($uri === '/oauth/token') {
            return $this->server->token($request);
        }

        if ($uri === '/resource') {
            return $this->server->resource($request);
        }

        throw new \Exception('Invalid request.');
    }
}
