<?php

namespace Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Domain\Model\UserCredentials;
use felpado as f;

class IterableUserCredentialsChecker implements UserCredentialsCheckerInterface
{
    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function check(UserCredentials $userCredentials)
    {
        $user = $this->findUser($userCredentials->getUsername());

        if (f\not($user)) {
            return false;
        }

        return f\get($user, 'password') === $userCredentials->getPassword();
    }

    private function findUser($username)
    {
        $isUser = function ($user) use ($username) {
            return f\get($user, 'username') === $username;
        };

        return f\find($isUser, $this->users);
    }
}
