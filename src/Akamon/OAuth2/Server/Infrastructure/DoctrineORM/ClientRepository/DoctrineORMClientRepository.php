<?php

namespace Akamon\OAuth2\Server\Infrastructure\DoctrineORM\ClientRepository;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
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
        $persistedClient = new PersistentClient($this->filterParamsFromDomain($client->getParams()));

        $this->em->persist($persistedClient);
        $this->em->flush();

        return new Client($this->filterParamsToDomain($persistedClient->getParams()));
    }

    /**
     * @return void
     */
    public function update(Client $client)
    {
        $persistedClient = $this->getRepository()->findOneBy(['oauth2Id' => f\get($client, 'id')]);
        $persistedClient->setParams($this->filterParamsFromDomain($client->getParams()));

        $this->em->persist($persistedClient);
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function remove(Client $client)
    {
        $this->em
            ->createQuery(sprintf('delete %s e where e.oauth2Id = :id', $this->getEntityClass()))
            ->execute(['id' => f\get($client, 'id')]);
    }

    /**
     * @return Client|null
     */
    public function find($id)
    {
        $persistedClient = $this->getRepository()->findOneBy(['oauth2Id' => $id]);

        return $persistedClient ? new Client($this->filterParamsToDomain($persistedClient->getParams())) : null;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $persistedClients = $this->getRepository()->findAll();

        $clients = [];
        foreach ($persistedClients as $p) {
            $clients[f\get($this->filterParamsToDomain($p->getParams()), 'id')] = new Client($this->filterParamsToDomain($p->getParams()));
        }

        return $clients;
    }

    private function getRepository()
    {
        return $this->em->getRepository($this->getEntityClass());
    }

    private function getEntityClass()
    {
        return 'Akamon\OAuth2\Server\Infrastructure\DoctrineORM\ClientRepository\PersistentClient';
    }

    private function filterParamsFromDomain($params)
    {
        return f\assoc($params, 'oauth2Id', f\get($params, 'id'));
    }

    private function filterParamsToDomain($params)
    {
        return f\rename_key($params, 'oauth2Id', 'id');
    }
}
