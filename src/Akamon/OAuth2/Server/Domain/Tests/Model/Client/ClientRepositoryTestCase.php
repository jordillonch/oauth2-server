<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Model\Client;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use felpado as f;

abstract class ClientRepositoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientRepositoryInterface
     */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->createRepository();
    }

    abstract protected function createRepository();

    public function testAddShouldSetAnIdToNewClients()
    {
        $newClient1 = new Client([
            'name' => 'pablodip',
            'secret' => 'foo',
            'allowedGrantTypes' => ['password'],
            'allowedScopes' => ['read', 'write'],
            'defaultScope' => 'read'
        ]);
        $newClient2 = new Client('bar');

        $addedClient1 = $this->repository->add($newClient1);
        $addedClient2 = $this->repository->add($newClient2);

        $this->assertNotNull(f\get($addedClient1, 'id'));
        $this->assertNotNull(f\get($addedClient2, 'id'));
        $this->assertNotEquals(f\get($addedClient1, 'id'), f\get($addedClient2, 'id'));
    }

    public function testUpdateShouldUpdateAClient()
    {
        $newClient = new Client('foo');

        $savedClient = $this->repository->add($newClient);
        $updatedClient = new Client(array_merge($savedClient->getParams(), ['name' => 'bar']));
        $this->repository->update($updatedClient);

        $this->assertEquals([
            f\get($updatedClient, 'id') => $updatedClient
        ], $this->repository->findAll());
    }

    public function testRemoveShouldRemoveAClient()
    {
        $newClient1 = new Client('foo');
        $newClient2 = new Client('bar');

        $addedClient1 = $this->repository->add($newClient1);
        $addedClient2 = $this->repository->add($newClient2);
        $this->repository->remove($addedClient1);

        $this->assertEquals([
            f\get($addedClient2, 'id') => $addedClient2
        ], $this->repository->findAll());
    }

    public function testFindShouldReturnAClientById()
    {
        $newClient1 = new Client(['name' => 'foo', 'allowedGrantTypes' => ['password']]);
        $newClient2 = new Client('bar');

        $addedClient1 = $this->repository->add($newClient1);
        $addedClient2 = $this->repository->add($newClient2);

        $this->assertEquals($addedClient1, $this->repository->find(f\get($addedClient1, 'id')));
        $this->assertEquals($addedClient2, $this->repository->find(f\get($addedClient2, 'id')));
    }

    public function testFindShouldReturnNullIfTheClientDoesNotExist()
    {
        $this->assertNull($this->repository->find('no'));
    }

    public function testFindAllShouldReturnAnEmptyArrayWhenThereAreNoClients()
    {
        $this->assertSame([], $this->repository->findAll());
    }

    public function testFindAllShouldReturnAllClients()
    {
        $newClient1 = new Client('foo');
        $newClient2 = new Client('bar');

        $addedClient1 = $this->repository->add($newClient1);
        $addedClient2 = $this->repository->add($newClient2);

        $this->assertEquals([
            f\get($addedClient1, 'id') => $addedClient1,
            f\get($addedClient2, 'id') => $addedClient2
        ], $this->repository->findAll());
    }
}
