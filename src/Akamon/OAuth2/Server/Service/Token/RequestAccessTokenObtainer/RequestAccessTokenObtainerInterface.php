<?php

namespace Akamon\OAuth2\Server\Service\Token\RequestAccessTokenObtainer;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Symfony\Component\HttpFoundation\Request;

interface RequestAccessTokenObtainerInterface
{
    /**
     * @return AccessToken
     */
    function obtain(Request $request);
}
