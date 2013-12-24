<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\RandomGenerator;


use Akamon\OAuth2\Server\Service\Token\RandomGenerator\IrcmaxellRandomGenerator;
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
