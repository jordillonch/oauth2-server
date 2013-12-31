<?php

namespace Akamon\OAuth2\Server\Model\User;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class UserCredentialsCollection
    extends ArrayCollection
    implements UserIdObtainerInterface
{
    public function getUserId($username)
    {
        $id = $this->filterByUsername($username)->key();

        if (null === $id) {
            throw new UserNotFoundException();
        }

        return $id;
    }

    public function filterByUsername($username)
    {
        return $this->filter(
            function (UserCredentials $user) use ($username) {
                return $user->getUsername() === $username;
            }
        );
    }
}
