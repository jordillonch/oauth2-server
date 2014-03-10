<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Client;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use felpado as f;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClientConstructor()
    {
        $client = new Client('pablodip');

        $this->assertSame('pablodip', f\get($client, 'id'));
    }

    public function testConstructorMinimumParameters()
    {
        $id = 'pablodip';
        $client = new \Akamon\OAuth2\Server\Domain\Model\Client\Client(['id' => $id]);

        $this->assertSame([
            'id' => $id,
            'secret' => null,
            'allowedGrantTypes' => array(),
            'allowedScopes' => array(),
            'defaultScope' => null
        ], $client->getParams());
    }

    public function testConstructorFullParameters()
    {
        $params = [
            'id' => 'paolo',
            'secret' => 'foo',
            'allowedGrantTypes' => array('password'),
            'allowedScopes' => array('read'),
            'defaultScope' => 'bar'
        ];

        $client = new \Akamon\OAuth2\Server\Domain\Model\Client\Client($params);
        $this->assertSame($params, $client->getParams());
    }

    public function testCheckSecretShouldReturnTrueWhenTheSecretIsRight()
    {
        $client = new Client(['id' => 'pablodip', 'secret' => '123']);

        $this->assertTrue($client->checkSecret('123'));
    }

    public function testCheckSecretShouldReturnFalseWhenTheSecretIsNotRight()
    {
        $client = new Client(['id' => 'pablodip', 'secret' => '123']);

        $this->assertFalse($client->checkSecret('321'));
    }

    public function testHasAllowedGrantType()
    {
        $client = new Client(['id' => 'pablodip', 'allowedGrantTypes' => ['foo']]);

        $this->assertTrue($client->hasAllowedGrantType('foo'));
        $this->assertFalse($client->hasAllowedGrantType('bar'));
    }

    public function testHasAllowedScope()
    {
        $client = new Client(['id' => 'pablodip', 'allowedScopes' => ['foo']]);

        $this->assertTrue($client->hasAllowedScope('foo'));
        $this->assertFalse($client->hasAllowedScope('bar'));
    }
}
