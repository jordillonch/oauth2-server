<?php

namespace Akamon\OAuth2\Server\Tests\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Service\AccessTokenCreator\AccessTokenCreator;
use Akamon\OAuth2\Server\Model\Client\Client;
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
        $this->tokenGenerator = \Mockery::mock('Akamon\OAuth2\Server\Service\TokenGenerator\TokenGeneratorInterface');

        $params = ['type' => $this->type, 'lifetime' => $this->lifetime];
        $this->creator = new AccessTokenCreator($this->tokenGenerator, $params);
    }

    public function testCreateShouldCreateAnAccessToken()
    {
        $token = sha1('foo');
        $this->tokenGenerator->shouldReceive('generate')->with(40)->once()->andReturn($token);

        $client = new Client(['id' => 'ups', 'name' => 'pablodip']);
        $userId = 'bar';
        $scope = 'scope';

        $context = new Context($client, $userId, $scope);
        $accessToken = $this->creator->create($context);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Model\AccessToken\AccessToken', $accessToken);
        $this->assertSame(array(
            'token' => $token,
            'type' => 'bearer',
            'clientId' => f\get($client, 'id'),
            'userId' => $userId,
            'expiresAt' => time() + $this->lifetime,
            'scope' => $scope
        ), $accessToken->getParams());
    }
}
