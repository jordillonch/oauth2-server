<?php

namespace Akamon\OAuth2\Server\Domain\Model;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;

class Context
{
    private $client;
    private $userId;
    private $scopes;

    public function __construct(Client $client, $userId, ScopeCollection $scopes)
    {
        $this->client = $client;
        $this->userId = $userId;
        $this->scopes = $scopes;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getScopes()
    {
        return $this->scopes;
    }
}
