<?php

namespace Akamon\OAuth2\Server\Tests\Model\RefreshToken;

use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;

class RefreshTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $params = [
            'token' => 'bar',
            'accessTokenToken' => 'foo',
            'expiresAt' => '123'
        ];

        $refreshToken = new RefreshToken($params);
        $this->assertSame($params, $refreshToken->getParams());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsLessThanTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'expiresAt' => time() - 1
        ]);

        $this->assertTrue($refreshToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsEqualToTime()
    {
        $refreshToken = new RefreshToken([
                'accessTokenToken' => 'foo',
                'token' => 'bar',
                'expiresAt' => time()
        ]);

        $this->assertTrue($refreshToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsGreatherThanTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'expiresAt' => time() + 1
        ]);

        $this->assertFalse($refreshToken->isExpired());
    }

    public function testGetLifetimeShouldReturnTheExpiredAtMinusTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'expiresAt' => time() + 60
        ]);

        $this->assertSame(60, $refreshToken->getLifetime());
    }

    public function testGetLifetimeShouldReturn0WhenTheLifetimeIsNegative()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'expiresAt' => time() - 1
        ]);

        $this->assertSame(0, $refreshToken->getLifetime());
    }
}
