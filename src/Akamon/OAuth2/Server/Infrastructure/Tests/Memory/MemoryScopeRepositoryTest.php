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
}
