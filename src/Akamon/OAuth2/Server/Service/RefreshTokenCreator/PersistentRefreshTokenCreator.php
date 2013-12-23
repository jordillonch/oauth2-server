<?php

namespace Akamon\OAuth2\Server\Service\RefreshTokenCreator;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface;

class PersistentRefreshTokenCreator implements RefreshTokenCreatorInterface
{
    private $delegate;
    private $repository;

    public function __construct(RefreshTokenCreatorInterface $delegate, RefreshTokenRepositoryInterface $repository)
    {
        $this->delegate = $delegate;
        $this->repository = $repository;
    }

    /**
     * @return \Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken
     */
    public function create(AccessToken $accessToken)
    {
        $refreshToken = $this->delegate->create($accessToken);

        $this->repository->add($refreshToken);

        return $refreshToken;
    }
}
