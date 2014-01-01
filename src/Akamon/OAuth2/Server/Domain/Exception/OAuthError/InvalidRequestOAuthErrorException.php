<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class InvalidRequestOAuthErrorException extends OAuthErrorException
{
    public function __construct($errorMessage = null)
    {
        parent::__construct(400, 'invalid_request', $errorMessage);
    }
}
