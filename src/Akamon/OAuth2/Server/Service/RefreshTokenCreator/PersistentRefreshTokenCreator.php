<?php

namespace Akamon\OAuth2\Server\Service\RefreshTokenCreator;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshTokenRepositoryInterface;
use felpado as f;

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
        $refreshToken = $this->generateRefreshToken($accessToken);

        $this->repository->add($refreshToken);

        return $refreshToken;
    }

    private function generateRefreshToken(AccessToken $accessToken)
    {
        $refreshToken = $this->delegate->create($accessToken);
        if ($this->refreshTokenExits($refreshToken)) {
            return $this->generateRefreshToken($accessToken);
        }

        return $refreshToken;
    }

    private function refreshTokenExits(RefreshToken $refreshToken)
    {
        return (Bool) $this->repository->find(f\get($refreshToken, 'token'));
    }
}
