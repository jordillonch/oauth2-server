<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class ScopeNotFoundOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('Invalid scope.');
    }
}
