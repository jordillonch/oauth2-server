<?php

namespace Akamon\OAuth2\Server\Tests\Model;

use Akamon\OAuth2\Server\Model\User\UserCredentials;

class UserCredentialsTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCredentials()
    {
        $username = 'foo';
        $password = 'bar';

        $userCredentials = new UserCredentials($username, $password);

        $this->assertSame($username, $userCredentials->getUsername());
        $this->assertSame($password, $userCredentials->getPassword());
    }
}
