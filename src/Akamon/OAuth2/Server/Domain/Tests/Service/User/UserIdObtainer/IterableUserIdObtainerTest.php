<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\User\UserIdObtainer;

use Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class IterableUserIdObtainerTest extends OAuth2TestCase
{
    private $users;

    /** @var \Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->users = [
            ['id' => 11, 'username' => 'foo'],
            ['id' => 21, 'username' => 'bar']
        ];

        $this->obtainer = new \Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer($this->users);
    }

    public function testReturnsAnId()
    {
        $this->assertSame(11, $this->obtainer->getUserId('foo'));
        $this->assertSame(21, $this->obtainer->getUserId('bar'));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\UserNotFoundException
     */
    public function testThrowsAnExceptionIfTheUserDoesNotExist()
    {
        $this->obtainer->getUserId('no');
    }
}
