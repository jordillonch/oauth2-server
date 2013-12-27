<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

class InvalidAccessTokenOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct($errorMessage = 'Invalid access token.')
    {
        parent::__construct($errorMessage);
    }
}
