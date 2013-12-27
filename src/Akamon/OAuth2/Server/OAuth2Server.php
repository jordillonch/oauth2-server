<?php

namespace Akamon\OAuth2\Server;

use Akamon\OAuth2\Server\Controller\ResourceController;
use Akamon\OAuth2\Server\Controller\TokenController;
use Symfony\Component\HttpFoundation\Request;

class OAuth2Server
{
    private $tokenController;
    private $resourceController;

    public function __construct(TokenController $tokenController, ResourceController $resourceController)
    {
        $this->tokenController = $tokenController;
        $this->resourceController = $resourceController;
    }

    public function token(Request $request)
    {
        return $this->tokenController->execute($request);
    }

    public function resource(Request $request)
    {
        return $this->resourceController->execute($request);
    }
}
