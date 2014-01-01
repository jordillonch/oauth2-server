<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\RandomGenerator;

use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator\RandomGeneratorInterface;
use felpado as f;

abstract class RandomGeneratorTestCase extends OAuth2TestCase
{
    /**
     * @return RandomGeneratorInterface
     */
    abstract protected function createGenerator();

    /**
     * @dataProvider provideGenerateString
     */
    public function testGenerateString($length, $characters)
    {
        $generator = $this->createGenerator();

        $string = $generator->generateString($length, $characters);

        $this->assertSame($length, strlen($string));

        $allStringCharactersInCharacters =f\every(f\partial('in_array', f\_(), str_split($characters)), str_split($string));
        $this->assertTrue($allStringCharactersInCharacters);
    }

    public function provideGenerateString()
    {
        return [
            [3, 'abc'],
            [10, '123abc'],
            [32, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-._'],
        ];
    }
}
