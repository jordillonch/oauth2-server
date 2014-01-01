<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\CallbackUserIdObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class CallbackUserIdObtainerTest extends OAuth2TestCase
{
    public function testGetUserId()
    {
        $callback = function ($username) {
            $this->assertSame('pablodip', $username);

            return 2;
        };
        $obtainer = new CallbackUserIdObtainer($callback);

        $this->assertSame(2, $obtainer->getUserId('pablodip'));
    }
}
