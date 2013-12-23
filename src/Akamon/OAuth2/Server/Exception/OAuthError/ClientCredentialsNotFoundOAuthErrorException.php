<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;

class ClientCredentialsNotFoundOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        parent::__construct(400, 'invalid_request', 'Client credentials are required.');
    }
}
