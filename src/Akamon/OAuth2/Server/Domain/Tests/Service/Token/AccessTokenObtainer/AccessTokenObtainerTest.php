<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\AccessTokenObtainer;

use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenObtainer\AccessTokenObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class AccessTokenObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $repository;
    /** @var AccessTokenObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->repository = $this->mock('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface');

        $this->obtainer = new AccessTokenObtainer($this->repository);
    }

    public function testObtainOk()
    {
        $token = md5(microtime().rand());
        $accessToken = $this->createAccessToken(['token' => $token]);

        $this->repository->shouldReceive('find')->with($token)->once()->andReturn($accessToken);

        $data = ['token' => $token];
        $this->assertSame($accessToken, $this->obtainer->obtain($data));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testObtainThrowsAnExceptionIfDataDoesNotHaveToken()
    {
        $this->obtainer->obtain(['foo' => 'bar']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testObtainThrowsAnExceptionIfDataTokenIsNotAString()
    {
        $this->obtainer->obtain(['token' => true]);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\AccessTokenNotFoundOAuthErrorException
     */
    public function testObtainThrowsAnExceptoinIfTheTokenDoesNotExist()
    {
        $token = md5(microtime().rand());

        $this->repository->shouldReceive('find')->with($token)->once()->andReturnNull();

        $data = ['token' => $token];
        $this->obtainer->obtain($data);
    }
}
