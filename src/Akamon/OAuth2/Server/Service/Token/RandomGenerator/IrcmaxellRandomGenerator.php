<?php

namespace Akamon\OAuth2\Server\Service\Token\RandomGenerator;

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
