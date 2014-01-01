<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyConsole;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClientCommand extends Command
{
    private $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('akamon:oauth2-server:client:create')
            ->setDescription('Creates a new client')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('secret', InputArgument::OPTIONAL)
            ->addOption('allowed-grant-type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
            ->addOption('allowed-scope', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
            ->addOption('default-scope', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client([
            'name' => $input->getArgument('name'),
            'secret' => $input->getArgument('secret'),
            'allowedGrantTypes' => $input->getOption('allowed-grant-type'),
            'allowedScopes' => $input->getOption('allowed-scope'),
            'defaultScope' => $input->getOption('default-scope')
        ]);

        $this->repository->add($client);

        $output->writeln('Client added.');
    }
}

