<?php

namespace Akamon\OAuth2\Server\Tests;

use Akamon\OAuth2\Server\OAuth2Server;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuth2ServerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $tokenController;
    /** @var MockInterface */
    private $resourceController;

    /** @var OAuth2Server */
    private $server;

    protected function setUp()
    {
        $this->tokenController = $this->mock('Akamon\OAuth2\Server\Controller\TokenController');
        $this->resourceController = $this->mock('Akamon\OAuth2\Server\Controller\ResourceController');

        $this->server = new OAuth2Server($this->tokenController, $this->resourceController);
    }

    public function testToken()
    {
        $request = new Request();
        $response = new Response();

        $this->tokenController->shouldReceive('execute')->with($request)->once()->andReturn($response);

        $this->assertSame($response, $this->server->token($request));
    }

    public function testResource()
    {
        $request = new Request();
        $response = new Response();

        $this->resourceController->shouldReceive('execute')->with($request)->once()->andReturn($response);

        $this->assertSame($response, $this->server->resource($request));
    }
}
