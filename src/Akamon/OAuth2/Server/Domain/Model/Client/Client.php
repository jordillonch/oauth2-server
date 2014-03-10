<?php

namespace Akamon\OAuth2\Server\Domain\Model\Client;

use felpado as f;

class Client implements \IteratorAggregate
{
    private $params;

    public function __construct($params)
    {
        $newParams = is_string($params) ? ['id' => $params] : $params;

        $this->params = f\fill_validating_or_throw($newParams, $this->getParamsRules());
    }

    private function getParamsRules()
    {
        return [
            'id' => f\required(['v' => 'is_scalar']),
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
        return array_search($grantType, f\get($this, 'allowedGrantTypes')) !== false;
    }

    public function hasAllowedScope($scope)
    {
        return array_search($scope, f\get($this, 'allowedScopes')) !== false;
    }
}
