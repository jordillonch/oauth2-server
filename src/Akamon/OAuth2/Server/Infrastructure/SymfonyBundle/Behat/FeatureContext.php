<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyBundle\Behat;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Akamon\Behat\SymfonyKernelContext\SymfonyKernelBehatContext;

class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('symfony_kernel', new SymfonyKernelBehatContext());
    }
}
