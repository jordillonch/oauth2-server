<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyHttpKernel;

use Akamon\OAuth2\Server\Domain\OAuth2Server;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SymfonyHttpKernelAkamonOAuth2FirewallEventSubscriber implements EventSubscriberInterface
{
    private $oauth2Server;
    private $secureRequestChecker;
    private $resourceProcessor;

    public function __construct(OAuth2Server $oauth2Server, $secureRequestChecker, $resourceProcessor)
    {
        $this->oauth2Server = $oauth2Server;
        $this->secureRequestChecker = $secureRequestChecker;
        $this->resourceProcessor = $resourceProcessor;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!call_user_func($this->secureRequestChecker, $request)) {
            return;
        }

        $response = $this->oauth2Server->resource($event->getRequest(), $this->resourceProcessor);
        if (!is_null($response)) {
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 8)
        );
    }
}
