<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class ExpiredAccessTokenOAuthErrorException extends InvalidAccessTokenOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('Expired access token.');
    }
}
