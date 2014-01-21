<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Controller;

use Akamon\OAuth2\Server\Domain\Controller\ResourceController;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\OAuthErrorException;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;

class ResourceControllerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $requestAccessTokenObtainer;
    /** @var MockInterface */
    private $processor;

    /** @var ResourceController */
    private $controller;

    protected function setUp()
    {
        $this->requestAccessTokenObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainerInterface');
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

        $this->controller = new ResourceController($this->requestAccessTokenObtainer);

        $this->assertSame($response, $this->controller->execute($request, $processor));
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

        $this->controller = new ResourceController($this->requestAccessTokenObtainer);

        $response = $this->controller->execute($request, $processor);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(json_encode($error->getParameters(), true), $response->getContent());

        $this->assertSame([], $calls);
    }
}
