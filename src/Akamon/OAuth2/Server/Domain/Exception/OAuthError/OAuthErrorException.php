<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class OAuthErrorException extends \Exception
{
    private $httpStatusCode;
    private $parameters = [];
    private $headers = [];

    public function __construct($httpStatusCode, $error, $errorMessage = null)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->addParameter('error', $error);
        if ($errorMessage !== null) {
            $this->addParameter('message', $errorMessage);
        }
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
