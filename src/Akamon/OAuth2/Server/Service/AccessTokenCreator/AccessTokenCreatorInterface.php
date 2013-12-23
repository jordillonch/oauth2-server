<?php

namespace Akamon\OAuth2\Server\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;

interface AccessTokenCreatorInterface
{
    /**
     * @return \Akamon\OAuth2\Server\Model\AccessToken\AccessToken
     */
    function create(Context $context);
}
