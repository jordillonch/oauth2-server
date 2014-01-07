<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;

class ScopeExistenceContextResolver implements ContextResolverInterface
{
    private $repository;

    public function __construct(ScopeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function resolve(Context $context)
    {
        if (is_null($this->repository->find($context->getScope()))) {
            throw new ScopeNotFoundOAuthErrorException();
        }

        return $context;
    }
}
