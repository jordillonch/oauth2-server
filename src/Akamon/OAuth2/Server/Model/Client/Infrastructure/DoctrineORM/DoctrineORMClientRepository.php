<?php

namespace Akamon\OAuth2\Server\Model\Client\Infrastructure\DoctrineORM;

use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface;
use Doctrine\ORM\EntityManager;
use felpado as f;

class DoctrineORMClientRepository implements ClientRepositoryInterface
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return Client
     */
    public function add(Client $client)
    {
        $persistedClient = new PersistentClient($client->getParams());

        $this->em->persist($persistedClient);
        $this->em->flush();

        return new Client($persistedClient->getParams());
    }

    /**
     * @return void
     */
    public function update(Client $client)
    {
        $persistedClient = $this->getRepository()->find(f\get($client, 'id'));
        $persistedClient->setParams($client->getParams());

        $this->em->persist($persistedClient);
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function remove(Client $client)
    {
        $this->em
            ->createQuery(sprintf('delete %s e where e.id = :id', $this->getEntityClass()))
            ->execute(['id' => f\get($client, 'id')]);
    }

    /**
     * @return Client|null
     */
    public function find($id)
    {
        $persistedClient = $this->getRepository()->find($id);

        return $persistedClient ? new Client($persistedClient->getParams()) : null;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $persistedClients = $this->getRepository()->findAll();

        $clients = [];
        foreach ($persistedClients as $p) {
            $clients[f\get($p->getParams(), 'id')] = new Client($p->getParams());
        }

        return $clients;
    }

    private function getRepository()
    {
        return $this->em->getRepository($this->getEntityClass());
    }

    private function getEntityClass()
    {
        return 'Akamon\OAuth2\Server\Model\Client\Infrastructure\DoctrineORM\PersistentClient';
    }
}
