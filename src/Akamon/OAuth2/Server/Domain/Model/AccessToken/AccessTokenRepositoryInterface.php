<?php

namespace Akamon\OAuth2\Server\Domain\Model\AccessToken;


interface AccessTokenRepositoryInterface
{
    /**
     * @param AccessToken $accessToken
     *
     * @return bool
     */
    function add(AccessToken $accessToken);

    /**
     * @param AccessToken $accessToken
     *
     * @return bool
     */
    function remove(AccessToken $accessToken);

    /**
     * @param $token
     *
     * @return AccessToken
     */
    function find($token);
}
