<?php

namespace Akamon\OAuth2\Server\Model\RefreshToken;

use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;

interface RefreshTokenRepositoryInterface
{
    function add(RefreshToken $refreshToken);

    function remove(RefreshToken $refreshToken);

    /**
     * @return RefreshToken|null
     */
    function find($token);
}
