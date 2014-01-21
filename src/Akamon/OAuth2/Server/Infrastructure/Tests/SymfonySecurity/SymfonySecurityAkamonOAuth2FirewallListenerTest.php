<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\SymfonySecurity;

use Akamon\MockeryCallableMock\MockeryCallableMock;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Infrastructure\SymfonySecurity\SymfonySecurityAkamonOAuth2FirewallListener;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonySecurityAkamonOAuth2FirewallListenerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $server;

    protected function setUp()
    {
        $this->server = $this->mock('Akamon\OAuth2\Server\Domain\OAuth2Server');
    }

    public function testItCallsOAuth2ServerResourceAndReturnsTheResponseIfItIsReturned()
    {
        $request = new Request();
        $response = new Response();
        $resourceProcessor = new MockeryCallableMock();

        $listener = new SymfonySecurityAkamonOAuth2FirewallListener($this->server, $resourceProcessor);

        $this->server->shouldReceive('resource')->with($request, $resourceProcessor)->once()->andReturn($response, $resourceProcessor);

        $httpKernel = $this->mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $event = new GetResponseEvent($httpKernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->assertNull($listener->handle($event));
        $this->assertSame($response, $event->getResponse());
    }

    public function testItCallsOAuth2ServerResourceAndReturnsTheResponseIfItIsNotReturned()
    {
        $request = new Request();
        $resourceProcessor = new MockeryCallableMock();

        $listener = new SymfonySecurityAkamonOAuth2FirewallListener($this->server, $resourceProcessor);

        $this->server->shouldReceive('resource')->with($request, $resourceProcessor)->once()->andReturnNull();

        $httpKernel = $this->mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $event = new GetResponseEvent($httpKernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->assertNull($listener->handle($event));
        $this->assertNull($event->getResponse());
    }
}
