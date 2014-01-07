<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\ContextResolvedTokenCreator;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;

class ContextResolvedTokenCreatorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $delegate;
    /** @var MockInterface */
    private $contextResolver;

    /** @var ContextResolvedTokenCreator */
    private $creator;

    protected function setUp()
    {
        $this->delegate = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface');
        $this->contextResolver = $this->mock('Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver\ContextResolverInterface');

        $this->creator = new ContextResolvedTokenCreator($this->delegate, $this->contextResolver);
    }

    public function testOk()
    {
        $context = $this->createContextMock();
        $contextResolved = $this->createContextMock();

        $result = new \stdClass();

        $this->contextResolver->shouldReceive('resolve')->with($context)->once()->andReturn($contextResolved)->ordered();
        $this->delegate->shouldReceive('create')->with(\Mockery::mustBe($contextResolved))->once()->andReturn($result)->ordered();

        $this->assertSame($result, $this->creator->create($context));
    }
}
