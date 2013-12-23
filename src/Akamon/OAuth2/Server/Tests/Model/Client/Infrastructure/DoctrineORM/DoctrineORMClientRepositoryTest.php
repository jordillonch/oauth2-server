<?php

namespace Akamon\OAuth2\Server\Tests\Model\Client\Infrastructure\DoctrineORM;

use Akamon\OAuth2\Server\Model\Client\Infrastructure\DoctrineORM\DoctrineORMClientRepository;
use Akamon\OAuth2\Server\Tests\Model\Client\ClientRepositoryTestCase;
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
        $config = Setup::createConfiguration();

        $prefixes = [
            realpath(__DIR__ . '/../../../../../Model/Client/Infrastructure/DoctrineORM') => 'Akamon\OAuth2\Server\Model\Client\Infrastructure\DoctrineORM'
        ];
        $driver = new SimplifiedXmlDriver($prefixes);
        $driver->setGlobalBasename('mapping');
        $config->setMetadataDriverImpl($driver);

        $em = EntityManager::create($dbParams, $config);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        return new DoctrineORMClientRepository($em);
    }
}
