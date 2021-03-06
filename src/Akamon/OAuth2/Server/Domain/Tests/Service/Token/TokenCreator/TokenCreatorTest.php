<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreator;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\Mock;
use felpado as f;

class TokenCreatorTest extends OAuth2TestCase
{
    /** @var Mock */
    private $accessTokenCreator;
    /** @var Mock */
    private $scopeResolver;

    /** @var TokenCreator */
    private $tokenCreator;

    protected function setUp()
    {
        $this->accessTokenCreator = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\AccessTokenCreatorInterface');
        $this->scopeResolver = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\AccessTokenCreatorInterface');

        $this->tokenCreator = new TokenCreator($this->accessTokenCreator);
    }

    public function testCreate()
    {
        $context = $this->createContextMock();
        $accessToken = $this->createAccessToken();

        $this->accessTokenCreator->shouldReceive('create')->once()->with($context)->andReturn($accessToken);

        $response = $this->tokenCreator->create($context);

        $this->assertSame([
            'access_token' => f\get($accessToken, 'token'),
            'token_type' => f\get($accessToken, 'type'),
            'expires_in' => f\get($accessToken, 'lifetime'),
            'scope' => f\get($accessToken, 'scope')
        ], $response);
    }
}
