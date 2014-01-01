<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidRequestOAuthErrorException;

class GrantTypeNotFoundOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('The grant type is required.');
    }
}
