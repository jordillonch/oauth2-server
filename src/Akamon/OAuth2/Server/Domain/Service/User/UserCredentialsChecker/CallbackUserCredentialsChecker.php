<?php

namespace Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Domain\Model\UserCredentials;

class CallbackUserCredentialsChecker implements UserCredentialsCheckerInterface
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function check(UserCredentials $userCredentials)
    {
        return call_user_func($this->callback, $userCredentials);
    }
}
