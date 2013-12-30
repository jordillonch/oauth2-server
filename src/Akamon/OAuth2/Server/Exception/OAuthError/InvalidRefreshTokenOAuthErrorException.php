<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

class InvalidRefreshTokenOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('Refresh token is invalid.');
    }
}
