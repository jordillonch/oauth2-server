<?php

namespace Akamon\OAuth2\Server\Tests\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Service\User\UserIdObtainer\InMemoryUserIdObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;

class InMemoryUserIdObtainerTest extends OAuth2TestCase
{
    /** @var InMemoryUserIdObtainer */
    private $obtainer;

    protected function setUp()
    {
        $users = [
            11 => 'pablodip',
            14 => 'pablo'
        ];
        $this->obtainer = new InMemoryUserIdObtainer($users);
    }

    public function testGetUserId()
    {
        $this->assertSame(11, $this->obtainer->getUserId('pablodip'));
        $this->assertSame(14, $this->obtainer->getUserId('pablo'));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\UserNotFoundException
     */
    public function testGetUserIdThrowsAnExceptionIfTheUserDoesNotExist()
    {
        $this->obtainer->getUserId('no');
    }
}
