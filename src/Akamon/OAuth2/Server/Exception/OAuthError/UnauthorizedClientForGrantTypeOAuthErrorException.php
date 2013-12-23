<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;

class UnauthorizedClientForGrantTypeOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        parent::__construct(400, 'unauthorized_client', 'The client is unauthorized for the grant type.');
    }
}
