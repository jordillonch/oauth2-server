<?php

namespace Akamon\OAuth2\Server\Service\User\UserIdObtainer\Infrastructure;

use Akamon\OAuth2\Server\Exception\UserNotFoundException;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\UserIdObtainerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use felpado as f;

class SymfonyUserIdObtainer implements UserIdObtainerInterface
{
    private $userProvider;
    private $method;

    public function __construct(UserProviderInterface $provider, $method = 'getUsername')
    {
        $this->userProvider = $provider;
        $this->method = $method;
    }

    /**
     * @return string
     *
     * @throws UserNotFoundException
     */
    public function getUserId($username)
    {
        $method = f\method($this->method);

        return $method($this->findUser($username));
    }

    private function findUser($username)
    {
        try {
            return $this->userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            throw new UserNotFoundException();
        }
    }
}
