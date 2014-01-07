<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Scope\ScopesObtainer;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ScopesObtainerTest extends OAuth2TestCase
{
    public function testItCreatesACollectionWithTheScope()
    {
        $obtainer = new ScopesObtainer();

        $inputData = ['scope' => 'foo'];
        $coll = new ScopeCollection([new Scope('foo')]);

        $this->assertEquals($coll, $obtainer->getScopes($inputData));
    }
}
