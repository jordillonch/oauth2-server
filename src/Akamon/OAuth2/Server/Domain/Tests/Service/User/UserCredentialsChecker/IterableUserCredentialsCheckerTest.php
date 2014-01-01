<?php

namespace Akamon\OAuth2\Server\Domain\Tests\Service\User\UserCredentialsChecker;

use Akamon\OAuth2\Server\Domain\Model\UserCredentials;
use Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;

class IterableUserCredentialsCheckerTest extends OAuth2TestCase
{
    private $users;

    /** @var \Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker */
    private $checker;

    protected function setUp()
    {
        $this->users = [
            ['username' => 'foo', 'password' => 'pass'],
            ['username' => 'bar', 'password' => 'ups']
        ];

        $this->checker = new \Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker($this->users);
    }

    public function testReturnsTrueWhenOk()
    {
        $this->assertTrue($this->checker->check(new UserCredentials('foo', 'pass')));
        $this->assertTrue($this->checker->check(new UserCredentials('bar', 'ups')));
    }

    public function testReturnsFalseWhenNok()
    {
        $this->assertFalse($this->checker->check(new UserCredentials('foo', 'ups')));
        $this->assertFalse($this->checker->check(new UserCredentials('bar', 'pass')));
    }

    public function testReturnsFalseWhenTheUserDoesNotExist()
    {
        $this->assertFalse($this->checker->check(new UserCredentials('pass', 'foo')));
        $this->assertFalse($this->checker->check(new UserCredentials('no', 'ups')));
    }
}
