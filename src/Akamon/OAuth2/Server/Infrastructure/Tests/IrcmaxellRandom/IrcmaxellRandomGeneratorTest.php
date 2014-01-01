<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\IrcmaxellRandom;


use Akamon\OAuth2\Server\Infrastructure\IrcmaxellRandom\IrcmaxellRandomGenerator;
use Akamon\OAuth2\Server\Domain\Tests\Service\Token\RandomGenerator\RandomGeneratorTestCase;
use RandomLib\Factory;

class IrcmaxellRandomGeneratorTest extends RandomGeneratorTestCase
{
    protected function createGenerator()
    {
        $ircmaxellFactory = new Factory();
        $ircmaxellGenerator = $ircmaxellFactory->getMediumStrengthGenerator();

        return new \Akamon\OAuth2\Server\Infrastructure\IrcmaxellRandom\IrcmaxellRandomGenerator($ircmaxellGenerator);
    }
}
