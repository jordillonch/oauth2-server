<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Scope;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ScopeCollectionTest extends OAuth2TestCase
{
    public function testConstruction()
    {
        $scopes = [new Scope('foo'), new Scope('bar')];

        $coll = new ScopeCollection($scopes);
        $this->assertSame($scopes, $coll->all());
    }

    public function testIterator()
    {
        $scopes = [new Scope('foo'), new Scope('bar')];

        $coll = new ScopeCollection($scopes);
        $this->assertEquals(new \ArrayIterator($scopes), $coll->getIterator());
    }

    public function testGetNames()
    {
        $scopes = [new Scope('foo'), new Scope('bar')];

        $coll = new ScopeCollection($scopes);
        $this->assertSame(['foo', 'bar'], $coll->getNames());
    }

    public function test__toString()
    {
        $scopes = [new Scope('foo'), new Scope('bar')];

        $coll = new ScopeCollection($scopes);
        $this->assertSame('foo bar', $coll->__toString());
    }

    public function testCreateFromString()
    {
        $this->assertEquals(new ScopeCollection([new Scope('foo')]), ScopeCollection::createFromString('foo'));
        $this->assertEquals(new ScopeCollection([new Scope('foo'), new Scope('bar')]), ScopeCollection::createFromString('foo bar'));
    }
}
