<?php

namespace Akamon\OAuth2\Server\Service\Token\AccessTokenObtainer;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;

interface AccessTokenObtainerInterface
{
    /**
     * @return AccessToken
     */
    function obtain(array $data);
}
