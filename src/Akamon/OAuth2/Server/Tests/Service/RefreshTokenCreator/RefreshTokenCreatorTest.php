<?php

namespace Akamon\OAuth2\Server\Tests\Service\RefreshTokenCreator;

use Akamon\OAuth2\Server\Service\RefreshTokenCreator\RefreshTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class RefreshTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $tokenGenerator;
    private $lifetime;

    /** @var RefreshTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->tokenGenerator = $this->mock('Akamon\OAuth2\Server\Service\TokenGenerator\TokenGeneratorInterface');
        $this->lifetime = 60;

        $this->creator = new RefreshTokenCreator($this->tokenGenerator, $this->lifetime);
    }

    public function testCreate()
    {
        $accessToken = $this->createAccessToken();
        $token = '123';

        $this->tokenGenerator->shouldReceive('generate')->once()->with(40)->andReturn($token);

        $refreshToken = $this->creator->create($accessToken);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken', $refreshToken);
        $this->assertSame([
            'token' => $token,
            'accessTokenToken' => f\get($accessToken, 'token'),
            'expiresAt' => time() + $this->lifetime
        ], $refreshToken->getParams());
    }
}
