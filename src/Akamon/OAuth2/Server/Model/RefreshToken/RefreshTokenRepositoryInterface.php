<?php

namespace Akamon\OAuth2\Server\Model\RefreshToken;


interface RefreshTokenRepositoryInterface
{
    /**
     * @param RefreshToken $refreshToken
     *
     * @return bool
     */
    function add(RefreshToken $refreshToken);

    /**
     * @param RefreshToken $refreshToken
     *
     * @return bool
     */
    function remove(RefreshToken $refreshToken);

    /**
     * @param $token
     *
     * @return RefreshToken
     */
    function find($token);
}
