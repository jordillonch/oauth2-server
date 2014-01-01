<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\RequestAccessTokenObtainer;

use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;
use Symfony\Component\HttpFoundation\Request;

interface RequestAccessTokenObtainerInterface
{
    /**
     * @return AccessToken
     */
    function obtain(Request $request);
}
