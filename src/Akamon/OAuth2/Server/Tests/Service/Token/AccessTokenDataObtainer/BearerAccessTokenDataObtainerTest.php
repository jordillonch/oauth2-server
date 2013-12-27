<?php

namespace Akamon\OAuth2\Server\Tests\Service\Token\AccessTokenObtainer;

use Akamon\OAuth2\Server\Service\Token\AccessTokenDataObtainer\BearerAccessTokenDataObtainer;
use Akamon\OAuth2\Server\Tests\OAuth2TestCase;
use Symfony\Component\HttpFoundation\Request;

class BearerAccessTokenDataTokenObtainerTest extends OAuth2TestCase
{
    public function testObtainOk()
    {
        $token = md5(microtime().rand());

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer '.$token);

        $obtainer = new BearerAccessTokenDataObtainer();

        $this->assertSame(array('token' => $token), $obtainer->obtain($request));
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\AccessTokenDataNotFoundOAuthErrorException
     * @dataProvider provideObtainNotFound
     */
    public function testObtainThrowsAnExceptionWhenTheDataIsNotFound($request)
    {
        $obtainer = new BearerAccessTokenDataObtainer();
        $obtainer->obtain($request);
    }

    public function provideObtainNotFound()
    {
        $emptyAuthorization = new Request();
        $emptyAuthorization->headers->set('authorization', '');

        $withoutToken = new Request();
        $withoutToken->headers->set('authorization', 'Bearer ');

        return [
            [new Request()],
            [$emptyAuthorization],
            [$withoutToken]
        ];
    }
}
