<?php

namespace Akamon\OAuth2\Server\Domain\Controller;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public static function createOAuthHttpResponse($statusCode, array $parameters, array $headers = [])
    {
        $content = json_encode($parameters, true);

        return new Response($content, $statusCode, [
                'content-type' => 'application/json',
                'cache-control' => 'no-store, private',
                'pragma' => 'no-cache'
            ] + $headers);
    }
}
