<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\RandomGenerator;

use Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator\ArrayRandRandomGenerator;

class ArrayRandRandomGeneratorTest extends RandomGeneratorTestCase
{
    protected function createGenerator()
    {
        return new ArrayRandRandomGenerator();
    }
}
