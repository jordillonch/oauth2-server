<?php

namespace Akamon\OAuth2\Server\Tests\Service\User\UserIdObtainer\Infrastructure;

use Akamon\OAuth2\Server\Service\User\UserIdObtainer\Infrastructure\SymfonyUserIdObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Mockery\MockInterface;
use Symfony\Component\Security\Core\User\User;

class SymfonyUserIdObtainerTest extends OAuth2TestCase
{
    /** @var MockInterface */
    private $userProvider;

    /** @var SymfonyUserIdObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->userProvider = $this->mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->obtainer = new SymfonyUserIdObtainer($this->userProvider);
    }

    public function testGetUserIdReturnsTheUsername()
    {
        $username = 'pablodip';
        $user = new User($username, 'pass');

        $this->userProvider->shouldReceive('loadUserByUsername')->with($username)->once()->andReturn($user);

        $this->assertSame($username, $this->obtainer->getUserId($username));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\UserNotFoundException
     */
    public function testGetUserIdShouldThrowAnexceptionIfTheUserDoesNotExist()
    {
        $username = 'pablodip';

        $this->userProvider->shouldReceive('loadUserByUsername')->with($username)->once()->andThrow('Symfony\Component\Security\Core\Exception\UsernameNotFoundException');

        $this->obtainer->getUserId($username);
    }
}
