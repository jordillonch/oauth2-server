<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonySecurity;

use Akamon\OAuth2\Server\Domain\OAuth2Server;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class SymfonySecurityAkamonOAuth2FirewallListener implements ListenerInterface
{
    private $oauth2Server;
    private $resourceProcessor;

    public function __construct(OAuth2Server $oauth2Server, $resourceProcessor)
    {
        $this->oauth2Server = $oauth2Server;
        $this->resourceProcessor = $resourceProcessor;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $response = $this->oauth2Server->resource($event->getRequest(), $this->resourceProcessor);
        if (!is_null($response)) {
            $event->setResponse($response);
        }
    }
}
