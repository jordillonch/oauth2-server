<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

use Akamon\OAuth2\Server\Exception\OAuthError\InvalidRequestOAuthErrorException;

class GrantTypeNotFoundOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('The grant type is required.');
    }
}
