<?php

namespace Akamon\OAuth2\Server\Controller;

use Akamon\OAuth2\Server\Service\Token\TokenGranter\TokenGranterInterface;
use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;
use Symfony\Component\HttpFoundation\Request;

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
            return Controller::createOAuthHttpResponse($e->getHttpStatusCode(), $e->getParameters(), $e->getHeaders());
        }

        return Controller::createOAuthHttpResponse(200, $responseParameters);
    }
}
