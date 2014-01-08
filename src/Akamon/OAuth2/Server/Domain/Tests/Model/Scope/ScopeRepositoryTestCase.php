<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Scope;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;
use felpado as f;

abstract class ScopeRepositoryTestCase extends OAuth2TestCase
{
    /** @var ScopeRepositoryInterface */
    private $repository;

    abstract protected function createRepository();

    protected function setUp()
    {
        $this->repository = $this->createRepository();
    }

    public function testIt()
    {
        $foo = new Scope('foo');
        $bar = new Scope('bar');

        $this->assertNull($this->repository->find('foo'));
        $this->assertNull($this->repository->find('bar'));

        $this->repository->add($foo);

        $this->assertEquals($foo, $this->repository->find('foo'));
        $this->assertNull($this->repository->find('bar'));

        $this->repository->add($bar);

        $this->assertEquals($foo, $this->repository->find('foo'));
        $this->assertEquals($bar, $this->repository->find('bar'));
    }
}
