<?php

namespace Akamon\OAuth2\Server\Infrastructure\Filesystem;

use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use felpado as f;

class FileClientRepository implements ClientRepositoryInterface
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Responsible for setting the id if it is new.
     */
    public function add(Client $client)
    {
        $clients = $this->allFromFile();

        $newId = max(f\conjoin(f\keys($clients), 0)) + 1;
        $newClient = new Client(array_merge($client->getParams(), ['id' => $newId]));

        $newClients = f\assoc($clients, $newId, $newClient->getParams());
        $this->allToFile($newClients);

        return $newClient;
    }

    public function update(Client $client)
    {
        $this->allToFile(f\assoc($this->allFromFile(), f\get($client, 'id'), $client->getParams()));
    }

    /**
     * Responsible for removing the id.
     */
    public function remove(Client $client)
    {
        $this->allToFile(f\dissoc($this->allFromFile(), f\get($client, 'id')));
    }

    public function find($id)
    {
        $all = $this->allFromFile();

        return f\contains($all, $id) ? new Client(f\get($all, $id)) : null;
    }

    public function findAll()
    {
        $newClient = function ($params) { return new Client($params); };

        return f\map($newClient, $this->allFromFile());
    }

    private function allFromFile()
    {
        if (!file_exists($this->file)) {
            return [];
        }

        $all = unserialize(file_get_contents($this->file));

        return $all === false ? [] : $all;
    }

    private function allToFile(array $all)
    {
        $this->writeFile(serialize($all));
    }

    private function writeFile($contents)
    {
        if (!file_exists(dirname($this->file))) {
            mkdir(dirname($this->file), 0777, true);
        }

        file_put_contents($this->file, $contents);
    }
}
