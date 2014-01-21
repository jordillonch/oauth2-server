<?php

namespace Akamon\OAuth2\Server\Domain\Controller;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\OAuthErrorException;
use Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ResourceController
{
    private $requestAccessTokenObtainer;

    public function __construct(RequestAccessTokenObtainerInterface $requestAccessTokenObtainer)
    {
        $this->requestAccessTokenObtainer = $requestAccessTokenObtainer;
    }

    public function execute(Request $request, $processor)
    {
        try {
            $accessToken = $this->requestAccessTokenObtainer->obtain($request);
        } catch (OAuthErrorException $e) {
            return Controller::createOAuthHttpResponse($e->getHttpStatusCode(), $e->getParameters(), $e->getHeaders());
        }

        return call_user_func($processor, $request, $accessToken);
    }
}
