<?php

namespace Akamon\OAuth2\Server\Domain\Controller;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\OAuthErrorException;
use Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ResourceController
{
    private $requestAccessTokenObtainer;
    private $processor;

    public function __construct(RequestAccessTokenObtainerInterface $requestAccessTokenObtainer, $processor)
    {
        $this->requestAccessTokenObtainer = $requestAccessTokenObtainer;
        $this->processor = $processor;
    }

    public function execute(Request $request)
    {
        try {
            $accessToken = $this->requestAccessTokenObtainer->obtain($request);
        } catch (OAuthErrorException $e) {
            return Controller::createOAuthHttpResponse($e->getHttpStatusCode(), $e->getParameters(), $e->getHeaders());
        }

        return call_user_func($this->processor, $request, $accessToken);
    }
}
