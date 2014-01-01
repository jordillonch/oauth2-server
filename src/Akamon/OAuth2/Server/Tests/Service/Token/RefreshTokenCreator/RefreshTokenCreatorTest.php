<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\RefreshTokenCreator;

use Akamon\OAuth2\Server\Service\Token\RefreshTokenCreator\RefreshTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class RefreshTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $tokenGenerator;
    private $lifetime = 60;

    /** @var RefreshTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->tokenGenerator = $this->mock('Akamon\OAuth2\Server\Service\Token\TokenGenerator\TokenGeneratorInterface');

        $params = ['lifetime' => $this->lifetime];
        $this->creator = new RefreshTokenCreator($this->tokenGenerator, $params);
    }

    public function testCreate()
    {
        $accessTokenToken = 'foo';
        $token = '123';

        $this->tokenGenerator->shouldReceive('generate')->once()->andReturn($token);

        $refreshToken = $this->creator->create($accessTokenToken);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken', $refreshToken);
        $this->assertSame([
            'token' => $token,
            'accessTokenToken' => 'foo',
            'createdAt' => time(),
            'lifetime' => $this->lifetime
        ], $refreshToken->getParams());
    }
}
