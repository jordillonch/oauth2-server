<?php

namespace Akamon\OAuth2\Server\Exception\OAuthError;

class InvalidClientCredentialsOAuthErrorException extends OAuthErrorException
{
    public function __construct()
    {
        parent::__construct(401, 'invalid_client', 'Client authentication failed.');

        $this->addHeader('www-authenticate', 'Basic realm="OAuth2"');
    }
}
