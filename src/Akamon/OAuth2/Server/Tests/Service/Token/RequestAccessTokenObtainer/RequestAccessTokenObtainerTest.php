<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\RequestAccessTokenObtainer;

use Akamon\OAuth2\Server\Service\Token\RequestAccessTokenObtainer\RequestAccessTokenObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestAccessTokenObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $accessTokenDataObtainer;
    /** @var MockInterface */
    private $accessTokenObtainer;

    /** @var RequestAccessTokenObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->accessTokenDataObtainer = $this->mock('Akamon\OAuth2\Server\Service\Token\AccessTokenDataObtainer\AccessTokenDataObtainerInterface');
        $this->accessTokenObtainer = $this->mock('Akamon\OAuth2\Server\Service\Token\AccessTokenObtainer\AccessTokenObtainerInterface');

        $this->obtainer = new RequestAccessTokenObtainer($this->accessTokenDataObtainer, $this->accessTokenObtainer);
    }

    public function testObtainOk()
    {
        $tokenData = array('a' => 1);
        $accessToken = $this->createAccessToken();
        $request = new Request();

        $this->accessTokenDataObtainer->shouldReceive('obtain')->with($request)->once()->andReturn($tokenData);
        $this->accessTokenObtainer->shouldReceive('obtain')->with($tokenData)->once()->andReturn($accessToken);

        $this->assertSame($accessToken, $this->obtainer->obtain($request));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\ExpiredAccessTokenOAuthErrorException
     */
    public function testObtainThrowsAnExceptionIfTheTokenIsExpired()
    {
        $tokenData = array('a' => 1);
        $accessToken = $this->createAccessToken(['createdAt' => time() - 61, 'lifetime' => 60]);
        $request = new Request();

        $this->accessTokenDataObtainer->shouldReceive('obtain')->with($request)->once()->andReturn($tokenData);
        $this->accessTokenObtainer->shouldReceive('obtain')->with($tokenData)->once()->andReturn($accessToken);

        $this->assertSame($accessToken, $this->obtainer->obtain($request));
    }
}
