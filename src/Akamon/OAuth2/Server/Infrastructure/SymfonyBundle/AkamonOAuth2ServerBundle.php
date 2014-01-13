<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyBundle;

use Akamon\OAuth2\Server\Infrastructure\SymfonyDependencyInjection\AkamonOAuth2ServerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkamonOAuth2ServerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new AkamonOAuth2ServerExtension();
    }
}
