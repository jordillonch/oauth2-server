<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\Behat\ApiContext\Domain\Model\Request;
use Akamon\Behat\ApiContext\Domain\Service\ClientRequester\ClientRequesterInterface;
use Akamon\Behat\ApiContext\Infrastructure\RequestConverter\SymfonyHttpFoundationRequestConverter;
use Akamon\Behat\ApiContext\Infrastructure\ResponseConverter\SymfonyHttpFoundationResponseConverter;
use Akamon\OAuth2\Server\Domain\OAuth2Server;
use Symfony\Component\HttpFoundation as Http;

class OAuth2Client implements ClientRequesterInterface
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
    public function request(Request $request)
    {
        $uri = $request->getUri();

        $requestConverter = new SymfonyHttpFoundationRequestConverter();
        $symfonyRequest = $requestConverter->convert($request);

        if ($uri === '/oauth/token') {
            $response =  $this->server->token($symfonyRequest);
        } else if ($uri === '/resource') {
            $response = $this->server->resource($symfonyRequest);
        } else {
            throw new \Exception('Invalid request.');
        }

        $responseConverter = new SymfonyHttpFoundationResponseConverter();

        return $responseConverter->convert($response);
    }
}
