<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

class ExpiredRefreshTokenOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('Refresh token is expired.');
    }
}
