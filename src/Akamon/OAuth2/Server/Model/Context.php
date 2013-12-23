<?php

namespace Akamon\OAuth2\Server\Model;

use Akamon\OAuth2\Server\Model\Client\Client;

class Context
{
    private $client;
    private $userId;
    private $scope;

    public function __construct(Client $client, $userId, $scope)
    {
        $this->client = $client;
        $this->userId = $userId;
        $this->scope = $scope;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
