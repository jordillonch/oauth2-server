<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenObtainer;

use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;

interface AccessTokenObtainerInterface
{
    /**
     * @return AccessToken
     */
    function obtain(array $data);
}
