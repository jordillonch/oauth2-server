<?php

namespace Akamon\OAuth2\Server\Tests;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;

class OAuth2TestCase extends \PHPUnit_Framework_TestCase
{
    protected function createClient(array $params = array())
    {
        return new Client(array_replace(['name' => 'client'.mt_rand()], $params));
    }

    protected function createAccessToken(array $params = array())
    {
        return new AccessToken(array_replace([
            'token' => sha1(microtime().mt_rand()),
            'type' => 'bearer',
            'clientId' => mt_rand(),
            'userId' => mt_rand(),
            'lifetime' => 3600
        ], $params));
    }

    protected function createRefreshToken(array $params = array())
    {
        return new RefreshToken(array_replace([
            'token' => sha1(microtime().mt_rand()),
            'accessTokenToken' => 'foo',
            'createdAt' => time(),
            'lifetime' => 3600
        ], $params));
    }

    /**
     * @return \Mockery\MockInterface|\Yay_MockObject
     */
    protected function createContextMock()
    {
        return $this->mock('Akamon\OAuth2\Server\Model\Context');
    }

    /**
     * @return \Mockery\MockInterface|\Yay_MockObject
     */
    protected function mock($class)
    {
        return \Mockery::mock($class);
    }
}
