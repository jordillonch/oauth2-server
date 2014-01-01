<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;

interface AccessTokenCreatorInterface
{
    /**
     * @return \Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken
     */
    function create(Context $context);
}
