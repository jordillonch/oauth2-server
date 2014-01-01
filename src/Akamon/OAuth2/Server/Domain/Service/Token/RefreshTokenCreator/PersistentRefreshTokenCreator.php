<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\RefreshTokenCreator;

use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface;
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
     * @return \Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshToken
     */
    public function create($accessTokenToken)
    {
        $refreshToken = $this->generateRefreshToken($accessTokenToken);

        $this->repository->add($refreshToken);

        return $refreshToken;
    }

    private function generateRefreshToken($accessTokenToken)
    {
        $refreshToken = $this->delegate->create($accessTokenToken);
        if ($this->refreshTokenExits($refreshToken)) {
            return $this->generateRefreshToken($accessTokenToken);
        }

        return $refreshToken;
    }

    private function refreshTokenExits(RefreshToken $refreshToken)
    {
        return (Bool) $this->repository->find(f\get($refreshToken, 'token'));
    }
}
