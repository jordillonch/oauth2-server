<?php

namespace Akamon\OAuth2\Server\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;

interface UserIdObtainerInterface
{
    /**
     * @param $username
     *
     * @throws UserNotFoundException
     * @return string
     */
    function getUserId($username);
}
