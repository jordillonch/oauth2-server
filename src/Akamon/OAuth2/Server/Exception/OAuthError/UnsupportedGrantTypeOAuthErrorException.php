<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;

class UnsupportedGrantTypeOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        $errorMessage = 'The grant type is not supported.';

        parent::__construct(400, 'unsupported_grant_type', $errorMessage);
    }
}
