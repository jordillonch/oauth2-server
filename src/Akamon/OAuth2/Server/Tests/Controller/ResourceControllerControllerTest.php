<?php

namespace Akamon\OAuth2\Server\Tests\Controller;

use Akamon\OAuth2\Server\Controller\ResourceController;
use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;

class ResourceControllerControllerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $requestAccessTokenObtainer;
    /** @var MockInterface */
    private $processor;

    /** @var ResourceController */
    private $controller;

    protected function setUp()
    {
        $this->requestAccessTokenObtainer = $this->mock('Akamon\OAuth2\Server\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainerInterface');
        $this->processor = $this->mock('stdClass');

        $this->controller = new ResourceController($this->requestAccessTokenObtainer, $this->processor);
        $this->controller = new ResourceController($this->requestAccessTokenObtainer, $this->processor);
    }

    public function testOk()
    {
        $request = new Request();
        $accessToken = $this->createAccessToken();
        $response = new \stdClass();

        $this->requestAccessTokenObtainer->shouldReceive('obtain')->with($request)->andReturn($accessToken);

        $calls = [];
        $processor = function () use (&$calls, $response) {
            $calls[] = func_get_args();

            return $response;
        };

        $this->controller = new ResourceController($this->requestAccessTokenObtainer, $processor);

        $this->assertSame($response, $this->controller->execute($request));
        $this->assertSame([[$request, $accessToken]], $calls);
    }

    public function testError()
    {
        $request = new Request();
        $error = new OAuthErrorException(400, 'invalid_request', 'foo');

        $this->requestAccessTokenObtainer->shouldReceive('obtain')->with($request)->andThrow($error);

        $calls = [];
        $processor = function () use (&$calls) {
            $calls[] = func_get_args();
        };

        $this->controller = new ResourceController($this->requestAccessTokenObtainer, $processor);

        $response = $this->controller->execute($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(json_encode($error->getParameters(), true), $response->getContent());

        $this->assertSame([], $calls);
    }
}
