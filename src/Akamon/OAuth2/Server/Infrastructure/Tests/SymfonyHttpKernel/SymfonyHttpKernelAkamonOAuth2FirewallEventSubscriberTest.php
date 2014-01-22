<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\SymfonyHttpKernel;

use Akamon\MockeryCallableMock\MockeryCallableMock;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Infrastructure\SymfonyHttpKernel\SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriber;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriberTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $server;

    protected function setUp()
    {
        $this->server = $this->mock('Akamon\OAuth2\Server\Domain\OAuth2Server');
    }

    public function testItDoesNothingIfTheRequestIsASubRequest()
    {
        $secureRequestChecker = new MockeryCallableMock();
        $resourceProcessor = new MockeryCallableMock();
        $subscriber = new SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriber($this->server, $secureRequestChecker, $resourceProcessor);

        $event = new GetResponseEvent($this->httpKernelMock(), $this->requestMock(), HttpKernelInterface::SUB_REQUEST);
        $this->assertNull($subscriber->onKernelRequest($event));
    }

    public function testItDoesNothingIfItIsNotASecuredRequest()
    {
        $secureRequestChecker = new MockeryCallableMock();
        $resourceProcessor = new MockeryCallableMock();
        $subscriber = new SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriber($this->server, $secureRequestChecker, $resourceProcessor);

        $httpKernel = $this->httpKernelMock();
        $request = $this->requestMock();
        $event = new GetResponseEvent($httpKernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $secureRequestChecker->should()->with($request)->once()->andReturn(false);

        $this->assertNull($subscriber->onKernelRequest($event));
    }

    public function testItReturnsTheServerResourceResponseIfItExists()
    {
        $secureRequestChecker = new MockeryCallableMock();
        $resourceProcessor = new MockeryCallableMock();
        $subscriber = new SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriber($this->server, $secureRequestChecker, $resourceProcessor);

        $httpKernel = $this->httpKernelMock();
        $request = $this->requestMock();
        $event = new GetResponseEvent($httpKernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $secureRequestChecker->should()->with($request)->once()->andReturn(true)->ordered();

        $response = new Response();
        $this->server->shouldReceive('resource')->with($request, $resourceProcessor)->once()->andReturn($response);

        $this->assertNull($subscriber->onKernelRequest($event));
        $this->assertSame($response, $event->getResponse());
    }

    private function httpKernelMock()
    {
        return $this->mock('Symfony\Component\HttpKernel\HttpKernelInterface');
    }

    private function requestMock()
    {
        return $this->mock('Symfony\Component\HttpFoundation\Request');
    }
}
