<?php

namespace Akamon\OAuth2\Server\Tests\Service\RandomGenerator;

use Akamon\OAuth2\Server\Service\RandomGenerator\ArrayRandRandomGenerator;

class ArrayRandRandomGeneratorTest extends RandomGeneratorTestCase
{
    protected function createGenerator()
    {
        return new ArrayRandRandomGenerator();
    }
}
