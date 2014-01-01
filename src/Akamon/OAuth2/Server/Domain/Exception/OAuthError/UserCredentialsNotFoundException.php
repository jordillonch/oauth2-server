<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidRequestOAuthErrorException;

class UserCredentialsNotFoundException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('The user credentials are required.');
    }
}
