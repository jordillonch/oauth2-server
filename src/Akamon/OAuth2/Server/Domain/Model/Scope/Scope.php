<?php

namespace Akamon\OAuth2\Server\Domain\Model\Scope;

use felpado as f;

class Scope implements \IteratorAggregate
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
            'name' => f\required(['v' => 'is_string']),
            'children' => f\optional(['v' => 'is_string'])
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
}
