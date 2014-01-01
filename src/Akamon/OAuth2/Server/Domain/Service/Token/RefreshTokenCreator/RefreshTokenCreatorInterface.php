<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\RefreshTokenCreator;

use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshToken;

interface RefreshTokenCreatorInterface
{
    /**
     * @return RefreshToken
     */
    function create($accessTokenTokenToken);
}
