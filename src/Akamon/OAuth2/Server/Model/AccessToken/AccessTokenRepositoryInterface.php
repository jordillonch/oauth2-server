<?php

namespace Akamon\OAuth2\Server\Model\AccessToken;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;

interface AccessTokenRepositoryInterface
{
    function add(AccessToken $accessToken);

    function remove(AccessToken $accessToken);

    function find($token);
}
