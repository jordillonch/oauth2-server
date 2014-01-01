<?php

namespace Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Domain\Model\UserCredentials;

interface UserCredentialsCheckerInterface
{
    function check(UserCredentials $userCredentials);
}
