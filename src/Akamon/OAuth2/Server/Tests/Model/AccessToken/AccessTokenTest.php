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
            'lifetime' => 60
        ]);

        $this->assertSame([
            'token' => 'foo',
            'type' => 'bearer',
            'clientId' => '123',
            'userId' => 'bar',
            'createdAt' => time(),
            'lifetime' => 60,
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
            'createdAt' => 2,
            'lifetime' => 3600,
            'scope' => 'bar,ups'
        ]);

        $this->assertSame([
            'token' => 'foo',
            'type' => 'mac',
            'clientId' => '123',
            'userId' => 'bar',
            'createdAt' => 2,
            'lifetime' => 3600,
            'scope' => 'bar,ups'
        ], $accessToken->getParams());
    }

    public function testGetExpiresAtReturnsCreatedAtPlusLifetime()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'createdAt' => 3,
            'lifetime' => 200
        ]);

        $this->assertSame(203, $accessToken->expiresAt());
    }

    public function testIsExpiredShouldReturnTrueWhenTimeIsLessThanExpiresAt()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'createdAt' => time() - 60,
            'lifetime' => 59
        ]);

        $this->assertTrue($accessToken->isExpired());
    }

    public function testIsExpiredShouldReturnTrueWhenTimeIsEqualToExpiresAt()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'createdAt' => time() - 3600,
            'lifetime' => 3600
        ]);

        $this->assertTrue($accessToken->isExpired());
    }

    public function testIsExpiredShouldReturnFalseWhenTimeIsGreaterThanExpiresAt()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'createdAt' => time() - 60,
            'lifetime' => 61
        ]);

        $this->assertFalse($accessToken->isExpired());
    }

    public function testGetLifetimeFromNowShouldReturnExpiresAtLessTime()
    {
        $accessToken = new \Akamon\OAuth2\Server\Model\AccessToken\AccessToken([
            'clientId' => '123',
            'userId' => 'bar',
            'type' => 'bearer',
            'token' => 'foo',
            'createdAt' => time(),
            'lifetime' => 60
        ]);

        $this->assertSame($accessToken->expiresAt() - time(), $accessToken->lifetimeFromNow());
    }
}
