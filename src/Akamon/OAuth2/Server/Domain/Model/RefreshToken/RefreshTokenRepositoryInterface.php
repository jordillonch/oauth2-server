<?php

namespace Akamon\OAuth2\Server\Domain\Model\RefreshToken;


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
     * @return RefreshToken|null
     */
    function find($token);
}
