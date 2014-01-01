<?php

namespace Akamon\OAuth2\Server\Domain\Model\AccessToken;

use felpado as f;

class AccessToken implements \IteratorAggregate
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = f\fill_validating_or_throw($params, $this->getParamsRules());
    }

    private function getParamsRules()
    {
        return [
            'token' => f\required(['v' => 'is_string']),
            'type' => f\required(['v' => 'is_string']),
            'clientId' => f\required(['v' => 'is_scalar']),
            'userId' => f\required(['v' => 'is_scalar']),
            'createdAt' => f\optional(['v' => 'is_int', 'd' => time()]),
            'lifetime' => f\required(['v' => 'is_int']),
            'scope' => f\optional(['v' => 'is_string'])
        ];
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getParams());
    }

    public function expiresAt()
    {
        return f\get($this, 'createdAt') + f\get($this, 'lifetime');
    }

    public function isExpired()
    {
        return $this->expiresAt() <= time();
    }

    public function lifetimeFromNow()
    {
        return $this->expiresAt() - time();
    }
}
