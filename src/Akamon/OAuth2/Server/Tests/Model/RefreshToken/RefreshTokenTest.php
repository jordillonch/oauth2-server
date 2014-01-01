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
            'createdAt' => 2,
            'lifetime' => 3600
        ];

        $refreshToken = new RefreshToken($params);
        $this->assertSame($params, $refreshToken->getParams());
    }

    public function testGetExpiresAtReturnsCreatedAtPlusLifetime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'createdAt' => 3,
            'lifetime' => 200
        ]);

        $this->assertSame(203, $refreshToken->expiresAt());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsLessThanTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'createdAt' => time() - 60,
            'lifetime' => 59
        ]);

        $this->assertTrue($refreshToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsEqualToTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'createdAt' => time() - 3600,
            'lifetime' => 3600
        ]);

        $this->assertTrue($refreshToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsGreatherThanTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'createdAt' => time() - 60,
            'lifetime' => 61
        ]);

        $this->assertFalse($refreshToken->isExpired());
    }

    public function testGetLifetimeFromNowShouldReturnExpiresAtLessTime()
    {
        $refreshToken = new RefreshToken([
            'accessTokenToken' => 'foo',
            'token' => 'bar',
            'createdAt' => time(),
            'lifetime' => 60
        ]);

        $this->assertSame($refreshToken->expiresAt() - time(), $refreshToken->lifetimeFromNow());
    }
}
