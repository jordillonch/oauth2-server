<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\DoctrineORM;

use Akamon\OAuth2\Server\Infrastructure\DoctrineORM\ClientRepository\DoctrineORMClientRepository;
use Akamon\OAuth2\Server\Domain\Tests\Model\Client\ClientRepositoryTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

use felpado as f;

class DoctrineORMClientRepositoryTest extends ClientRepositoryTestCase
{
    protected function createRepository()
    {
        $dbParams = ['driver' => 'pdo_sqlite', 'memory' => true];
        $config = Setup::createConfiguration($devMode = true);

        $prefixes = [
            realpath(__DIR__ . '/../../../Infrastructure/DoctrineORM/ClientRepository') => 'Akamon\OAuth2\Server\Infrastructure\DoctrineORM\ClientRepository'
        ];
        $driver = new SimplifiedXmlDriver($prefixes);
        $driver->setGlobalBasename('mapping');
        $config->setMetadataDriverImpl($driver);

        $em = EntityManager::create($dbParams, $config);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        return new \Akamon\OAuth2\Server\Infrastructure\DoctrineORM\ClientRepository\DoctrineORMClientRepository($em);
    }
}
