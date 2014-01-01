<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ContextTest extends OAuth2TestCase
{
    public function testContext()
    {
        $client = $this->createClient();
        $userId = 'foo';
        $scope = 'bar';

        $context = new Context($client, $userId, $scope);

        $this->assertSame($client, $context->getClient());
        $this->assertSame($userId, $context->getUserId());
        $this->assertSame($scope, $context->getScope());
    }
}
