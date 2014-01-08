<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ContextTest extends OAuth2TestCase
{
    public function testContext()
    {
        $client = $this->createClient();
        $userId = 'foo';
        $scopes = new ScopeCollection([new Scope('foo')]);

        $context = new Context($client, $userId, $scopes);

        $this->assertSame($client, $context->getClient());
        $this->assertSame($userId, $context->getUserId());
        $this->assertSame($scopes, $context->getScopes());
    }
}
