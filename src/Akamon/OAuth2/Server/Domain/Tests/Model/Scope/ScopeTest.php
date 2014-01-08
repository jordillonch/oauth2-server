<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Scope;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ScopeTest extends OAuth2TestCase
{
    public function testConstructionMinimum()
    {
        $scope = new Scope(['name' => 'foo']);
        $this->assertSame([
            'name' => 'foo',
            'children' => null
        ], $scope->getParams());
    }

    public function testConstructionFull()
    {
        $params = [
            'name' => 'foo',
            'children' => 'bar'
        ];

        $scope = new Scope($params);
        $this->assertSame($params, $scope->getParams());
    }
}
