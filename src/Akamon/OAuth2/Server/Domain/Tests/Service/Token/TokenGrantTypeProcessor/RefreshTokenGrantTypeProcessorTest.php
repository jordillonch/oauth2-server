<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\RefreshTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class RefreshTokenGrantTypeProcessorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $refreshTokenRepository;
    /** @var MockInterface */
    private $accessTokenRepository;
    /** @var MockInterface */
    private $tokenCreator;

    /** @var RefreshTokenGrantTypeProcessor */
    private $processor;

    protected function setUp()
    {
        $this->refreshTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface');
        $this->accessTokenRepository = $this->mock('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface');
        $this->tokenCreator = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface');

        $this->processor = new RefreshTokenGrantTypeProcessor($this->refreshTokenRepository, $this->accessTokenRepository, $this->tokenCreator);
    }

    public function testGrantType()
    {
        $this->assertSame('refresh_token', $this->processor->getGrantType());
    }

    public function testOk()
    {
        $client = $this->createClient();

        $accessToken = $this->createAccessToken();
        $refreshToken = $this->createRefreshToken(['accessTokenToken' => f\get($accessToken, 'token')]);

        $context = new Context($client, f\get($accessToken, 'userId'), f\get($accessToken, 'scope'));

        $params = new \stdClass();

        $this->refreshTokenRepository->shouldReceive('find')->with(f\get($refreshToken, 'token'))->once()->andReturn($refreshToken)->ordered();
        $this->accessTokenRepository->shouldReceive('find')->with(f\get($refreshToken, 'accessTokenToken'))->andReturn($accessToken)->ordered();

        $this->refreshTokenRepository->shouldReceive('remove')->with($refreshToken)->once()->ordered();
        $this->accessTokenRepository->shouldReceive('remove')->with($accessToken)->once()->ordered();

        $this->tokenCreator->shouldReceive('create')->with(\Mockery::on(function ($v) use ($context) { return $v == $context; }))->once()->andReturn($params);

        $this->assertSame($params, $this->processor->process($client, ['refresh_token' => f\get($refreshToken, 'token')]));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\ExpiredRefreshTokenOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheRefreshTokenIsExpired()
    {
        $refreshToken = $this->createRefreshToken(['createdAt' => time(), 'lifetime' => 0]);

        $this->refreshTokenRepository->shouldReceive('find')->with(f\get($refreshToken, 'token'))->once()->andReturn($refreshToken);

        $this->processor->process($this->createClient(), ['refresh_token' => f\get($refreshToken, 'token')]);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidRefreshTokenOAuthErrorException
     */
    public function testItThrowsAnExceptionIfThereAccessTokenDoesNotExist()
    {
        $refreshToken = $this->createRefreshToken();

        $this->refreshTokenRepository->shouldReceive('find')->with(f\get($refreshToken, 'token'))->once()->andReturn($refreshToken)->ordered();
        $this->accessTokenRepository->shouldReceive('find')->with(f\get($refreshToken, 'accessTokenToken'))->andReturnNull()->ordered();

        $this->refreshTokenRepository->shouldReceive('remove')->with($refreshToken)->once()->ordered();

        $this->processor->process($this->createClient(), ['refresh_token' => f\get($refreshToken, 'token')]);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\RefreshTokenNotFoundOAuthErrorException
     */
    public function testItThrowsAnExceptionIfThereIsNoRefreshToken()
    {
        $this->processor->process($this->createClient(), []);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidRefreshTokenOAuthErrorException
     */
    public function testItThrowsAnExceptionIfTheRefreshTokenDoesNotExist()
    {
        $refreshToken = 'foo';

        $this->refreshTokenRepository->shouldReceive('find')->with($refreshToken)->once()->andReturnNull();

        $this->processor->process($this->createClient(), ['refresh_token' => $refreshToken]);
    }
}
