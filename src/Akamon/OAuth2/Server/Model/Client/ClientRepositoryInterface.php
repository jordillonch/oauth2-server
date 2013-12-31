<?php

namespace Akamon\OAuth2\Server\Model\Client;


interface ClientRepositoryInterface
{
    /**
     * @param Client $client
     *
     * @return Client
     */
    function add(Client $client);

    /**
     * @param Client $client
     *
     * @return void
     */
    function update(Client $client);

    /**
     * @param Client $client
     *
     * @return void
     */
    function remove(Client $client);

    /**
     * @param $id
     *
     * @return Client|null
     */
    function find($id);

    function findAll();
}
