<?php

namespace Akamon\OAuth2\Server\Tests\Service\RandomGenerator;


use Akamon\OAuth2\Server\Service\RandomGenerator\IrcmaxellRandomGenerator;
use RandomLib\Factory;

class IrcmaxellRandomGeneratorTest extends RandomGeneratorTestCase
{
    protected function createGenerator()
    {
        $ircmaxellFactory = new Factory();
        $ircmaxellGenerator = $ircmaxellFactory->getMediumStrengthGenerator();

        return new IrcmaxellRandomGenerator($ircmaxellGenerator);
    }
}
