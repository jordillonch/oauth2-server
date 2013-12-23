<?php

namespace Akamon\OAuth2\Server\Tests\Service\TokenGenerator;

use Akamon\OAuth2\Server\Service\TokenGenerator\BasicTokenGenerator;

class TokenGrantedResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerLength
     */
    public function testGenerateShouldGenerateATokenOfTheGivenLength($length)
    {
        $generator = new BasicTokenGenerator();

        $token = $generator->generate($length);
        $this->assertInternalType('string', $token);
        $this->assertSame($length, strlen($token));
    }

    public function providerLength()
    {
        return [
            [1],
            [10],
            [40],
            [20000]
        ];
    }
}
