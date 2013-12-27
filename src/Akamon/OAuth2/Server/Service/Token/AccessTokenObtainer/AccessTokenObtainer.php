<?php

namespace Akamon\OAuth2\Server\Service\Token\AccessTokenObtainer;

use Akamon\OAuth2\Server\Exception\OAuthError\AccessTokenNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use felpado as f;

class AccessTokenObtainer implements AccessTokenObtainerInterface
{
    private $repository;

    public function __construct(AccessTokenRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AccessToken
     */
    public function obtain(array $data)
    {
        $token = $this->getToken($data);
        $accessToken = $this->repository->find($token);

        if (f\not($accessToken)) {
            throw new AccessTokenNotFoundOAuthErrorException();
        }

        return $accessToken;
    }

    private function getToken($data)
    {
        $token = f\get($data, 'token');
        if (f\not(is_string($token))) {
            throw new \InvalidArgumentException('Token must be a strong.');
        }

        return $token;
    }
}
