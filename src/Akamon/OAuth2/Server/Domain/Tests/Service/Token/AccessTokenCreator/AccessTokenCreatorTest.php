<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\AccessTokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\AccessTokenCreator;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Mockery\MockInterface;
use felpado as f;

class AccessTokenCreatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockInterface */
    private $tokenGenerator;

    private $type = 'bearer';
    private $lifetime = 3600;

    /** @var AccessTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->tokenGenerator = \Mockery::mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenGenerator\TokenGeneratorInterface');

        $params = ['type' => $this->type, 'lifetime' => $this->lifetime];
        $this->creator = new AccessTokenCreator($this->tokenGenerator, $params);
    }

    public function testCreateShouldCreateAnAccessToken()
    {
        $token = sha1('foo');
        $this->tokenGenerator->shouldReceive('generate')->once()->andReturn($token);

        $client = new Client(['id' => 'ups', 'name' => 'pablodip']);
        $userId = 'bar';
        $scopes = new ScopeCollection([new Scope('foo'), new Scope('bar')]);

        $context = new Context($client, $userId, $scopes);
        $accessToken = $this->creator->create($context);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken', $accessToken);
        $this->assertSame(array(
            'token' => $token,
            'type' => 'bearer',
            'clientId' => f\get($client, 'id'),
            'userId' => $userId,
            'createdAt' => time(),
            'lifetime' => $this->lifetime,
            'scope' => 'foo bar'
        ), $accessToken->getParams());
    }
}
