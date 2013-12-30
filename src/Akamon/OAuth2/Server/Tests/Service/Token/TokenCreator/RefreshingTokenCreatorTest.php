<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Service\Token\TokenCreator\RefreshingTokenCreator;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use felpado as f;

class RefreshingTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $delegate;
    /** @var MockInterface */
    private $refreshTokenCreator;

    /** @var RefreshingTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->delegate = $this->mock('Akamon\OAuth2\Server\Service\Token\TokenCreator\TokenCreatorInterface');
        $this->refreshTokenCreator = $this->mock('Akamon\OAuth2\Server\Service\Token\RefreshTokenCreator\RefreshTokenCreatorInterface');

        $this->creator = new RefreshingTokenCreator($this->delegate, $this->refreshTokenCreator);
    }

    public function testOk()
    {
        $refreshToken = $this->createRefreshToken();
        $params = ['access_token' => 'foo', 'token_type' => 'bar', 'lifetime' => 20];

        $context = $this->createContextMock();

        $this->delegate->shouldReceive('create')->with($context)->once()->andReturn($params)->ordered();
        $this->refreshTokenCreator->shouldReceive('create')->with($params['access_token'])->once()->andReturn($refreshToken);

        $expected = array_merge($params, ['refresh_token' => f\get($refreshToken, 'token')]);
        $this->assertSame($expected, $this->creator->create($context));
    }
}
