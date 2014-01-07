<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ComposedContextResolver;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class ComposedContextResolverTest extends OAuth2TestCase
{
    public function testIt()
    {
        $resolver1 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ContextResolverInterface');
        $resolver2 = $this->mock('Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ContextResolverInterface');

        $composed = new ComposedContextResolver([$resolver1, $resolver2]);

        $context1 = $this->createContextMock();
        $context2 = $this->createContextMock();
        $context3 = $this->createContextMock();

        $resolver1->shouldReceive('resolve')->with($context1)->once()->andReturn($context2);
        $resolver2->shouldReceive('resolve')->with($context2)->once()->andReturn($context3);

        $this->assertSame($context3, $composed->resolve($context1));
    }
}
