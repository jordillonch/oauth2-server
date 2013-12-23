<?php

namespace Akamon\OAuth2\Server\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;

class PersistentAccessTokenCreator implements AccessTokenCreatorInterface
{
    private $creator;
    private $repository;

    public function __construct(AccessTokenCreatorInterface $creator, AccessTokenRepositoryInterface $repository)
    {
        $this->creator = $creator;
        $this->repository = $repository;
    }

    public function create(Context $context)
    {
        $accessToken = $this->creator->create($context);

        $this->repository->add($accessToken);

        return $accessToken;
    }
}
