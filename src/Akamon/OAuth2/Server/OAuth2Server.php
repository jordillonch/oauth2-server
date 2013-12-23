<?php

namespace Akamon\OAuth2\Server;

use Akamon\OAuth2\Server\Controller\TokenController;
use Symfony\Component\HttpFoundation\Request;

class OAuth2Server
{
    private $tokenController;

    public function __construct(TokenController $tokenController)
    {
        $this->tokenController = $tokenController;
    }

    public function token(Request $request)
    {
        return $this->tokenController->execute($request);
    }
}
