<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyBundle;

use Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkamonOAuth2ServerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new AkamonOAuth2ServerExtension());
    }
}
