<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class AccessTokenDataNotFoundOAuthErrorException extends InvalidAccessTokenOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('The access token is required.');
    }
}
