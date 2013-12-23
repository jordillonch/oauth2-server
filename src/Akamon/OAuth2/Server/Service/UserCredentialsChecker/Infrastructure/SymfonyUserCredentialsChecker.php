<?php

namespace Akamon\OAuth2\Server\Service\UserCredentialsChecker\Infrastructure;

use Akamon\OAuth2\Server\Model\UserCredentials;
use Akamon\OAuth2\Server\Service\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SymfonyUserCredentialsChecker implements UserCredentialsCheckerInterface
{
    private $userProvider;
    private $encoderFactory;

    public function __construct(UserProviderInterface $userProvider, EncoderFactoryInterface $encoderFactory)
    {
        $this->userProvider = $userProvider;
        $this->encoderFactory = $encoderFactory;
    }

    public function check(UserCredentials $userCredentials)
    {
        $user = $this->findUser($userCredentials->getUsername());
        $encoder = $this->encoderFactory->getEncoder($user);

        $encoded = $user->getPassword();
        $raw = $userCredentials->getPassword();
        $salt = $user->getSalt();

        return $encoder->isPasswordValid($encoded, $raw, $salt);
    }

    private function findUser($username)
    {
        return $this->userProvider->loadUserByUsername($username);
    }
}
