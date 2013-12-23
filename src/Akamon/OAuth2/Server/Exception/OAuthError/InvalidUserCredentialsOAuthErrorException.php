<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;

class InvalidUserCredentialsOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        parent::__construct(400, 'invalid_grant', 'User authentication failed.');
    }
}
