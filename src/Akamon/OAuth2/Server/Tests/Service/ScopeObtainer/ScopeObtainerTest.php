<?php

namespace Akamon\OAuth2\Server\Tests\Service\ScopeObtainer;

use Akamon\OAuth2\Server\Service\ScopeObtainer\ScopeObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Symfony\Component\HttpFoundation\Request;

class ScopeObtainerTest extends OAuth2TestCase
{
    public function testGetScopeShouldReturnTheScopeFromTheRequestPostParameters()
    {
        $request = new Request();
        $request->request->set('scope', 'foo');

        $obtainer = new ScopeObtainer();

        $this->assertSame('foo', $obtainer->getScope($request));
    }
}
