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
            'createdAt' => f\required(['v' => 'is_int']),
            'lifetime' => f\required(['v' => 'is_int'])
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
