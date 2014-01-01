<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\OAuthErrorException;

class InvalidUserCredentialsOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        parent::__construct(400, 'invalid_grant', 'User authentication failed.');
    }
}
