<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\SymfonySecurity;

use Akamon\OAuth2\Server\Infrastructure\SymfonySecurity\SymfonyUserIdObtainer;
use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\Security\Core\User\User;

class SymfonyUserIdObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $userProvider;

    /** @var \Akamon\OAuth2\Server\Infrastructure\SymfonySecurity\SymfonyUserIdObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->userProvider = $this->mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->obtainer = new \Akamon\OAuth2\Server\Infrastructure\SymfonySecurity\SymfonyUserIdObtainer($this->userProvider);
    }

    public function testGetUserIdReturnsTheUsername()
    {
        $username = 'pablodip';
        $user = new User($username, 'pass');

        $this->userProvider->shouldReceive('loadUserByUsername')->with($username)->once()->andReturn($user);

        $this->assertSame($username, $this->obtainer->getUserId($username));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Domain\Exception\UserNotFoundException
     */
    public function testGetUserIdShouldThrowAnexceptionIfTheUserDoesNotExist()
    {
        $username = 'pablodip';

        $this->userProvider->shouldReceive('loadUserByUsername')->with($username)->once()->andThrow('Symfony\Component\Security\Core\Exception\UsernameNotFoundException');

        $this->obtainer->getUserId($username);
    }
}
