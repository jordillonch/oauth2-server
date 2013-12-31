<?php

namespace Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Domain\Exception\UserNotFoundException;
use felpado as f;

class IterableUserIdObtainer implements UserIdObtainerInterface
{
    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * @return string
     *
     * @throws UserNotFoundException
     */
    public function getUserId($username)
    {
        return f\get($this->findUser($username), 'id');
    }

    private function findUser($username)
    {
        $getUser = function ($user) use ($username) {
            return f\get($user, 'username') === $username;
        };

        $user = f\find($getUser, $this->users);

        if (f\not($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
