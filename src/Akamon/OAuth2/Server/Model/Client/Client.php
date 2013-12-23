<?php

namespace Akamon\OAuth2\Server\Model\Client;

use felpado as f;

class Client implements \IteratorAggregate
{
    private $params;

    public function __construct($params)
    {
        $newParams = is_string($params) ? ['name' => $params] : $params;

        $this->params = f\fill_validating_or_throw($newParams, $this->getParamsRules());
    }

    private function getParamsRules()
    {
        return [
            'id' => f\optional(['v' => 'is_scalar']),
            'name' => f\required(['v' => 'is_string']),
            'secret' => f\optional(['v' => 'is_scalar']),
            'allowedGrantTypes' => f\optional(['v' => 'is_array', 'd' => []]),
            'allowedScopes' => f\optional(['v' => 'is_array', 'd' => []]),
            'defaultScope' => f\optional(['v' => 'is_string'])
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

    public function checkSecret($secret)
    {
        return f\get($this, 'secret') === $secret;
    }

    public function hasAllowedGrantType($grantType)
    {
        return f\contains(f\get($this, 'allowedGrantTypes'), $grantType);
    }
}
