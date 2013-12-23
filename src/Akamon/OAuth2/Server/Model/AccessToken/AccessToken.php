<?php

namespace Akamon\OAuth2\Server\Model\AccessToken;

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
            'expiresAt' => f\required(['v' => 'is_numeric']),
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

    public function isExpired()
    {
        return f\get($this, 'expiresAt') <= time();
    }

    public function getLifetime()
    {
        return max(f\get($this, 'expiresAt') - time(), 0);
    }
}
