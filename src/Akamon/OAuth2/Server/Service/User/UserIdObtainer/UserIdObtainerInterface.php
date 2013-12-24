<?php

namespace Akamon\OAuth2\Server\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;

interface UserIdObtainerInterface
{
    /**
     * @return string
     *
     * @throws UserNotFoundException
     */
    function getUserId($username);
}
