<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;

interface TokenCreatorInterface
{
    /**
     * @return array An array of parameters.
     */
    function create(Context $context);
}
