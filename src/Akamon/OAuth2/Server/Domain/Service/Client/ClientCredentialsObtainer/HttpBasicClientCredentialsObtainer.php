<?php

namespace Akamon\OAuth2\Server\Domain\Service\Client\ClientCredentialsObtainer;

use Akamon\OAuth2\Server\Domain\Model\Client\ClientCredentials;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\ClientCredentialsNotFoundOAuthErrorException;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicClientCredentialsObtainer implements ClientCredentialsObtainerInterface
{
    public function getClientCredentials(Request $request)
    {
        if ($request->headers->has('PHP_AUTH_USER')) {
            return new ClientCredentials(
                $request->headers->get('PHP_AUTH_USER'),
                $request->headers->get('PHP_AUTH_PW')
            );
        }

        throw new ClientCredentialsNotFoundOAuthErrorException();
    }
}
