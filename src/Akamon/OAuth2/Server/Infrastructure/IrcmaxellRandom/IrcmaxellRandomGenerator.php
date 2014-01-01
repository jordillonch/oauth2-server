<?php

namespace Akamon\OAuth2\Server\Infrastructure\IrcmaxellRandom;

use Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator\RandomGeneratorInterface;
use RandomLib\Generator;

class IrcmaxellRandomGenerator implements RandomGeneratorInterface
{
    private $ircmaxellGenerator;

    public function __construct(Generator $ircmaxellGenerator)
    {
        $this->ircmaxellGenerator = $ircmaxellGenerator;
    }

    public function generateString($length, $characters)
    {
        return $this->ircmaxellGenerator->generateString($length, $characters);
    }
}
