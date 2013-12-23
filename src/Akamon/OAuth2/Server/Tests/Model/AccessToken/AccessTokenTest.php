<?php

namespace Akamon\OAuth2\Server\Tests\Model\AccessToken;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorMinimumParameters()
    {
        $accessToken = new AccessToken([
            'token' => 'foo',
            'type' => 'bearer',
            'clientId' => '123',
            'userId' => 'bar',
            'expiresAt' => '456'
        ]);

        $this->assertSame([
            'token' => 'foo',
            'type' => 'bearer',
            'clientId' => '123',
            'userId' => 'bar',
            'expiresAt' => '456',
            'scope' => null
        ], $accessToken->getParams());
    }

    public function testConstructorFullParameters()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'token' => 'foo',
            'type' => 'mac',
            'clientId' => '123',
            'userId' => 'bar',
            'expiresAt' => '456',
            'scope' => 'bar,ups'
        ]);

        $this->assertSame([
            'token' => 'foo',
            'type' => 'mac',
            'clientId' => '123',
            'userId' => 'bar',
            'expiresAt' => '456',
            'scope' => 'bar,ups'
        ], $accessToken->getParams());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsLessThanTime()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'expiresAt' => time() - 1
        ]);

        $this->assertTrue($accessToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsEqualToTime()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'expiresAt' => time()
        ]);

        $this->assertTrue($accessToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenExpiredAtIsGreatherThanTime()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'expiresAt' => time() + 1
        ]);

        $this->assertFalse($accessToken->isExpired());
    }

    public function testGetLifetimeShouldReturnTheExpiredAtMinusTime()
    {
        $accessToken = new AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'expiresAt' => time() + 60
        ]);

        $this->assertSame(60, $accessToken->getLifetime());
    }

    public function testGetLifetimeShouldReturn0WhenTheLifetimeIsNegative()
    {
        $accessToken = new AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'expiresAt' => time() - 1
        ]);

        $this->assertSame(0, $accessToken->getLifetime());
    }
}
