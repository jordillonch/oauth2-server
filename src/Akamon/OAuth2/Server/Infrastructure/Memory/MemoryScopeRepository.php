<?php

namespace Akamon\OAuth2\Server\Infrastructure\Memory;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;
use felpado as f;

class MemoryScopeRepository implements ScopeRepositoryInterface
{
    private $scopes = [];

    public function __construct($scopes = array())
    {
        $createScope = function ($name) { return new Scope($name); };
        f\each([$this, 'add'], f\map($createScope, $scopes));
    }

    /**
     * @param Scope $scope
     *
     * @return void
     */
    public function add(Scope $scope)
    {
        $this->scopes[f\get($scope, 'name')] = $scope;
    }

    /**
     * @return Scope|null
     */
    public function find($name)
    {
        return f\get_or($this->scopes, $name, null);
    }
}
