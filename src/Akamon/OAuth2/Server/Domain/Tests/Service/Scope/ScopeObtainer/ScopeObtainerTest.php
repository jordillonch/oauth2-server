<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Scope\ScopeObtainer;

use Akamon\OAuth2\Server\Domain\Service\Scope\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ScopeObtainerTest extends OAuth2TestCase
{
    public function testGetScopeShouldReturnTheScopeFromTheRequestPostParameters()
    {
        $obtainer = new ScopeObtainer();

        $inputData = ['scope' => 'foo'];
        $this->assertSame('foo', $obtainer->getScope($inputData));
    }
}
