<?php

namespace Akamon\OAuth2\Server\Model\Client;

use Akamon\OAuth2\Server\Model\Client\Client;

interface ClientRepositoryInterface
{
    /**
     * @return Client
     */
    function add(Client $client);

    /**
     * @return void
     */
    function update(Client $client);

    /**
     * @return void
     */
    function remove(Client $client);

    /**
     * @return Client|null
     */
    function find($id);

    function findAll();
}
