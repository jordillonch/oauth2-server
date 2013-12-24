<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\TokenGenerator;

use Akamon\OAuth2\Server\Service\Token\RandomGenerator\ArrayRandRandomGenerator;
use Akamon\OAuth2\Server\Service\Token\TokenGenerator\BearerTokenGenerator;

class BearerTokenGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $randomGenerator = new ArrayRandRandomGenerator();
        $generator = new BearerTokenGenerator($randomGenerator);

        $token1 = $generator->generate();
        $token2 = $generator->generate();

        $this->assertInternalType('string', $token1);
        $this->assertInternalType('string', $token2);
        $this->assertNotEquals($token1, $token2);
    }
}
