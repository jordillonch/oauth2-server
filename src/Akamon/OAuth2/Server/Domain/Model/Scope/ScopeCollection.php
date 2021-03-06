<?php

namespace Akamon\OAuth2\Server\Domain\Model\Scope;

use felpado as f;

class ScopeCollection implements \IteratorAggregate
{
    private $scopes = [];

    public function __construct(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->add($scope);
        }
    }

    public function add(Scope $scope)
    {
        $this->scopes[] = $scope;
    }

    public function all()
    {
        return $this->scopes;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->scopes);
    }

    public function getNames()
    {
        return f\map(f\partial('felpado\get', f\_(), 'name'), $this);
    }

    public function isEmpty()
    {
        return !!!$this->scopes;
    }

    public function __toString()
    {
        return implode(' ', $this->getNames());
    }

    public static function createFromString($string)
    {
        if ($string) {
            $createScope = function ($name) { return new Scope($name); };
            $scopes = f\map($createScope, explode(' ', $string));
        } else {
            $scopes = [];
        }

        return new ScopeCollection($scopes);
    }
}
