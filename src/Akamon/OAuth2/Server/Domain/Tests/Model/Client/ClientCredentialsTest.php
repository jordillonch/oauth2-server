<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Client;

use Akamon\OAuth2\Server\Domain\Model\Client\ClientCredentials;

class ClientCredentialsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $clientCredentials = new ClientCredentials('123', '456');

        $this->assertSame('123', $clientCredentials->getId());
        $this->assertSame('456', $clientCredentials->getSecret());
    }
}
