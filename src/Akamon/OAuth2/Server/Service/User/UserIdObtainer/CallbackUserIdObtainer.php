<?php

namespace Akamon\OAuth2\Server\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;

class CallbackUserIdObtainer implements UserIdObtainerInterface
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return string
     *
     * @throws UserNotFoundException
     */
    public function getUserId($username)
    {
        return call_user_func($this->callback, $username);
    }
}
