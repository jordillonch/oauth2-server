<?php

namespace Akamon\OAuth2\Server\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use felpado as f;

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
        $accessToken = $this->generateAccessToken($context);

        $this->repository->add($accessToken);

        return $accessToken;
    }

    private function generateAccessToken(Context $context)
    {
        $accessToken = $this->creator->create($context);
        if ($this->accessTokenExits($accessToken)) {
            return $this->generateAccessToken($context);
        }

        return $accessToken;
    }

    private function accessTokenExits(AccessToken $accessToken)
    {
        return (Bool) $this->repository->find(f\get($accessToken, 'token'));
    }
}
