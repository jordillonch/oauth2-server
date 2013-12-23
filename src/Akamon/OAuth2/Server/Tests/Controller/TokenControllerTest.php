<?php

namespace Akamon\OAuth2\Server\Tests\Controller;

use Akamon\OAuth2\Server\Controller\TokenController;
use Akamon\OAuth2\Server\Exception\OAuthError\OAuthErrorException;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Symfony\Component\HttpFoundation\Request;
use Mockery\MockInterface;

class TokenControllerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $tokenGranter;
    /** @var TokenController */
    private $tokenController;

    protected function setUp()
    {
        $this->tokenGranter = $this->mock('Akamon\OAuth2\Server\Service\TokenGranter\TokenGranterInterface');
        $this->tokenController = new TokenController($this->tokenGranter);
    }

    public function testExecuteShouldGrantAToken()
    {
        $request = new Request();
        $responseParameters = [
            'access_token' => '123',
            'token_type' => 'bearer',
            'expires_in' => 60
        ];

        $this->tokenGranter->shouldReceive('grant')->once()->with($request)->andReturn($responseParameters);

        $response = $this->tokenController->execute($request);

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $this->assertSame('no-store, private', $response->headers->get('cache-control'));
        $this->assertSame('no-cache', $response->headers->get('pragma'));
        $this->assertSame(json_encode($responseParameters, true), $response->getContent());
    }

    public function testExecuteShouldCatchOAuthErrors()
    {
        $request = new Request();
        $error = new OAuthErrorException(123, 'error_string', 'Error message');
        $error->addParameter('foo', 'bar');
        $error->addHeader('foobar', 'barfoo');

        $this->tokenGranter->shouldReceive('grant')->once()->with($request)->andThrow($error);

        $response = $this->tokenController->execute($request);

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $response);
        $this->assertSame(123, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $this->assertSame('no-store, private', $response->headers->get('cache-control'));
        $this->assertSame('no-cache', $response->headers->get('pragma'));
        $this->assertSame('barfoo', $response->headers->get('foobar'));
        $this->assertSame(json_encode($error->getParameters(), true), $response->getContent());
    }
}
