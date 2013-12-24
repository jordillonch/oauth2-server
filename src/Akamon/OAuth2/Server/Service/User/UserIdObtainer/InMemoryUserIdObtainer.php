<?php

namespace Akamon\OAuth2\Server\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;

class InMemoryUserIdObtainer implements UserIdObtainerInterface
{
    private $users;

    /**
     * @param array $users An array of users with id as key and username as value.
     */
    public function __construct(array $users)
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
        $id = array_search($username, $this->users, true);
        if ($id === false) {
            throw new UserNotFoundException();
        }

        return $id;
    }
}
