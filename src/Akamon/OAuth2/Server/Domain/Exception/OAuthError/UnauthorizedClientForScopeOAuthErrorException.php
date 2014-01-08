<?php

namespace Akamon\OAuth2\Server\Domain\Exception\OAuthError;

class UnauthorizedClientForScopeOAuthErrorException extends InvalidRequestOAuthErrorException
{
    public function __construct()
    {
        parent::__construct('Scope not allowed.');
    }
}
