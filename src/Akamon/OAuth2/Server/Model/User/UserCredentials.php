<?php

namespace Akamon\OAuth2\Server\Model\User;

class UserCredentials
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->password = $password;
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
