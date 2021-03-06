<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\ExpiredAccessTokenOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenDataObtainer\AccessTokenDataObtainerInterface;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenObtainer\AccessTokenObtainerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestAccessTokenObtainer implements RequestAccessTokenObtainerInterface
{
    private $accessTokenDataObtainer;
    private $accessTokenObtainer;

    public function __construct(AccessTokenDataObtainerInterface $accessTokenDataObtainer, AccessTokenObtainerInterface $accessTokenObtainer)
    {
        $this->accessTokenDataObtainer = $accessTokenDataObtainer;
        $this->accessTokenObtainer = $accessTokenObtainer;
    }

    /**
     * @return AccessToken
     */
    public function obtain(Request $request)
    {
        $data = $this->accessTokenDataObtainer->obtain($request);
        $accessToken = $this->accessTokenObtainer->obtain($data);

        if ($accessToken->isExpired()) {
            throw new ExpiredAccessTokenOAuthErrorException();
        }

        return $accessToken;
    }
}
