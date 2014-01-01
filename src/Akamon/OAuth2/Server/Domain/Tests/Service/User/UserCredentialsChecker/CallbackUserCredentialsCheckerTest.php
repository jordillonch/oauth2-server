<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Domain\Model\UserCredentials;
use Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\CallbackUserCredentialsChecker;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class CallbackUserCredentialsCheckerTest extends OAuth2TestCase
{
    public function testCheck()
    {
        $pablodipCredentials = new UserCredentials('pablodip', 'pass');
        $callback = function (UserCredentials $userCredentials) use ($pablodipCredentials) {
            return $userCredentials == $pablodipCredentials;
        };
        $checker = new CallbackUserCredentialsChecker($callback);

        $this->assertTrue($checker->check(new UserCredentials('pablodip', 'pass')));
        $this->assertFalse($checker->check(new UserCredentials('pablodip', 'no')));
        $this->assertFalse($checker->check(new UserCredentials('foo', 'no')));
    }
}
