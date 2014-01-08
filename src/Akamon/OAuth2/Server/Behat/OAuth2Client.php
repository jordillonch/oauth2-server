<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\Behat\ApiContext\Client\ClientInterface;
use Akamon\OAuth2\Server\Domain\OAuth2Server;
use Symfony\Component\HttpFoundation as Http;

class OAuth2Client implements ClientInterface
{
    /** @var OAuth2Server */
    private $server;

    public function setServer(OAuth2Server $server)
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
