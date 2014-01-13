<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\Memory;

use Akamon\OAuth2\Server\Domain\Tests\Model\Scope\ScopeRepositoryTestCase;
use Akamon\OAuth2\Server\Infrastructure\Memory\MemoryScopeRepository;

class MemoryScopeRepositoryTest extends ScopeRepositoryTestCase
{
    protected function createRepository()
    {
        return new MemoryScopeRepository();
    }

    public function testConstruction()
    {
        $scopes = ['foo', 'bar'];
        $repo = new MemoryScopeRepository($scopes);

        $this->assertNotNull($repo->find('foo'));
        $this->assertNotNull($repo->find('bar'));
        $this->assertNull($repo->find('ups'));
    }
}
