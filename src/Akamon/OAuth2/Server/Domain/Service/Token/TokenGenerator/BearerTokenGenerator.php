<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGenerator;

use Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator\RandomGeneratorInterface;

class BearerTokenGenerator implements TokenGeneratorInterface
{
    const TOKEN_LENGTH = 32;
    const TOKEN_CHARACTERS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-._';

    private $randomGenerator;

    public function __construct(RandomGeneratorInterface $randomGenerator)
    {
        $this->randomGenerator = $randomGenerator;
    }

    public function generate()
    {
        return $this->randomGenerator->generateString(self::TOKEN_LENGTH, self::TOKEN_CHARACTERS);
    }
}
