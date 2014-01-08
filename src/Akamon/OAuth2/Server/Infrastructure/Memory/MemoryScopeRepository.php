<?php

namespace Akamon\OAuth2\Server\Infrastructure\Memory;

use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;
use felpado as f;

class MemoryScopeRepository implements ScopeRepositoryInterface
{
    private $scopes = [];

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
