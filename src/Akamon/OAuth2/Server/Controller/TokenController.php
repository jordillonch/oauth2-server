<?php

namespace Akamon\OAuth2\Server\Controller;

use Akamon\OAuth2\Server\Service\TokenGranter\TokenGranterInterface;
use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController
{
    private $tokenGranter;

    public function __construct(TokenGranterInterface $tokenGranter)
    {
        $this->tokenGranter = $tokenGranter;
    }

    public function execute(Request $request)
    {
        try {
            $responseParameters = $this->tokenGranter->grant($request);
        } catch (OAuthErrorException $e) {
            return $this->createHttpResponse($e->getHttpStatusCode(), $e->getParameters(), $e->getHeaders());
        }

        return $this->createHttpResponse(200, $responseParameters);
    }

    private function createHttpResponse($statusCode, array $parameters, array $headers = [])
    {
        $content = json_encode($parameters, true);

        return new Response($content, $statusCode, [
            'content-type' => 'application/json',
            'cache-control' => 'no-store, private',
            'pragma' => 'no-cache'
        ] + $headers);
    }
}
