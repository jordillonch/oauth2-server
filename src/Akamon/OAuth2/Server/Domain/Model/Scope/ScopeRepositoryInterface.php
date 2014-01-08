<?php

namespace Akamon\OAuth2\Server\Domain\Model\Scope;

interface ScopeRepositoryInterface
{
    /**
     * @param Scope $scope
     *
     * @return void
     */
    function add(Scope $scope);

    /**
     * @return Scope|null
     */
    function find($name);
}
