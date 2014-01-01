<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\SymfonyConsole;

use Akamon\OAuth2\Server\Infrastructure\SymfonyConsole\CreateClientCommand;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateClientCommandTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $repository;

    /** @var \Akamon\OAuth2\Server\Infrastructure\SymfonyConsole\CreateClientCommand */
    private $command;

    protected function setUp()
    {
        $this->repository = $this->mock('Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface');

        $this->command = new CreateClientCommand($this->repository);
    }

    public function testMinimumParameters()
    {
        $name = 'foo';

        $client = new Client(['name' => $name]);

        $this->repository->shouldReceive('add')->with(\Mockery::on(function ($v) use ($client) { return $v == $client; }))->once();

        $input = new ArrayInput(['name' => $name]);
        $this->assertSame(0, $this->command->run($input, new NullOutput()));
    }

    public function testFullParameters()
    {
        $name = 'foo';
        $secret = 'bar';
        $allowedGrantTypes = ['password', 'refresh'];
        $allowedScopes = ['here', 'there', 'all'];
        $defaultScope = 'ups';

        $client = new Client([
            'name' => $name,
            'secret' => $secret,
            'allowedGrantTypes' => $allowedGrantTypes,
            'allowedScopes' => $allowedScopes,
            'defaultScope' => $defaultScope
        ]);

        $this->repository->shouldReceive('add')->with(\Mockery::on(function ($v) use ($client) { return $v == $client; }))->once();

        $input = new ArrayInput([
            'name' => $name,
            'secret' => $secret,
            '--allowed-grant-type' => $allowedGrantTypes,
            '--allowed-scope' => $allowedScopes,
            '--default-scope' => $defaultScope
        ]);
        $this->assertSame(0, $this->command->run($input, new NullOutput()));
    }
}
