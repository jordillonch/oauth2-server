<?php

namespace Akamon\OAuth2\Server\Service\TokenCreator;

use Akamon\OAuth2\Server\Model\Context;

interface TokenCreatorInterface
{
    /**
     * @return array An array of parameters.
     */
    function create(Context $context);
}
