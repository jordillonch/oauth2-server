<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\Behat;

use Behat\Behat\Context\BehatContext;
use Pablodip\Behat\SymfonyContainerContext\SymfonyContainerBehatContext;

class FeatureContext extends BehatContext
{
    private $containerContext;

    public function __construct()
    {
        $this->containerContext = new SymfonyContainerBehatContext();

        $this->useContext('symfony_container', $this->containerContext);
    }
}
