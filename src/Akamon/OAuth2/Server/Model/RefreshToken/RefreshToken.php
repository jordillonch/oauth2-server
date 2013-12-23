<?php

namespace Akamon\OAuth2\Server\Model\RefreshToken;

use felpado as f;

class RefreshToken implements \IteratorAggregate
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
            'accessTokenToken' => f\required(['v' => 'is_string']),
            'expiresAt' => f\required(['v' => 'is_numeric'])
        ];
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->params);
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
