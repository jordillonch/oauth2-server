<?php

namespace Akamon\OAuth2\Server\Domain\Service\Context\ContextResolver;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\ScopeNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;
use felpado as f;

class ScopeExistenceContextResolver implements ContextResolverInterface
{
    private $repository;

    public function __construct(ScopeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function resolve(Context $context)
    {
        $scopeExists = [$this->repository, 'find'];
        $scopeNames = $context->getScopes()->getNames();

        if (f\some(f\not_fn($scopeExists), $scopeNames)) {
            throw new ScopeNotFoundOAuthErrorException();
        }

        return $context;
    }
}
