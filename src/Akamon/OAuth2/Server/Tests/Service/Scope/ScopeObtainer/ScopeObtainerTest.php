<?php

namespace Akamon\OAuth2\Server\Tests\Service\Scope\ScopeObtainer;

use Akamon\OAuth2\Server\Service\Scope\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;

class ScopeObtainerTest extends OAuth2TestCase
{
    public function testGetScopeShouldReturnTheScopeFromTheRequestPostParameters()
    {
        $obtainer = new ScopeObtainer();

        $inputData = ['scope' => 'foo'];
        $this->assertSame('foo', $obtainer->getScope($inputData));
    }
}
