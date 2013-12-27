<?php

namespace Akamon\OAuth2\Server\Service\Token\AccessTokenDataObtainer;

use Akamon\OAuth2\Server\Exception\OAuthError\AccessTokenDataNotFoundOAuthErrorException;
use Symfony\Component\HttpFoundation\Request;
use felpado as f;

class BearerAccessTokenDataObtainer implements AccessTokenDataObtainerInterface
{
    /**
     * @return array
     */
    public function obtain(Request $request)
    {
        if (f\not($request->headers->has('authorization'))) {
            throw new AccessTokenDataNotFoundOAuthErrorException();
        }

        $authorization = $request->headers->get('authorization');

        $regex = '/^Bearer (.+)$/';
        if (f\not(preg_match($regex, $authorization))) {
            throw new AccessTokenDataNotFoundOAuthErrorException();
        }

        $token = preg_replace($regex, '$1', $authorization);

        return array('token' => $token);
    }
}
