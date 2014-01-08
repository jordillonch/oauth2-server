<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\DirectTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;

class DirectTokenGrantTypeProcessorTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $scopesObtainer;
    /** @var MockInterface */
    private $tokenCreator;

    /** @var DirectTokenGrantTypeProcessor */
    private $processor;

    protected function setUp()
    {
        $this->scopesObtainer = $this->mock('Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface');
        $this->tokenCreator = $this->mock('Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface');

        $this->processor = new DirectTokenGrantTypeProcessor($this->scopesObtainer, $this->tokenCreator);
    }

    public function testOk()
    {
        $client = $this->createClient();
        $userId = '2';
        $scope = 'foo';
        $scopes = new ScopeCollection([new Scope($scope)]);

        $context = new Context($client, $userId, $scopes);

        $inputData = ['user_id' => $userId, 'scope' => $scope];
        $returnData = new \stdClass();

        $this->scopesObtainer->shouldReceive('getScopes')->with($inputData)->once()->andReturn($scopes);
        $this->tokenCreator->shouldReceive('create')->with(\Mockery::mustBe($context))->once()->andReturn($returnData);

        $this->assertSame($returnData, $this->processor->process($client, $inputData));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\UserNotFoundException
     */
    public function testItThrowsAnExceptionIfThereIsNoUserId()
    {
        $client = $this->createClient();
        $inputData = [];

        $this->processor->process($client, $inputData);
    }
}
