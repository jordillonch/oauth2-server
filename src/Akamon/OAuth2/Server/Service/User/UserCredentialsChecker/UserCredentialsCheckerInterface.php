<?php

namespace Akamon\OAuth2\Server\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Model\User\UserCredentials;

interface UserCredentialsCheckerInterface
{
    function check(UserCredentials $userCredentials);
}
