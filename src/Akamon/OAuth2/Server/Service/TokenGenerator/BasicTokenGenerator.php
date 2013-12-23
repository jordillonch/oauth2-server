<?php

namespace Akamon\OAuth2\Server\Service\TokenGenerator;

class BasicTokenGenerator implements TokenGeneratorInterface
{
    public function generate($length)
    {
        $token = '';
        while (strlen($token) < $length) {
            $token .= sha1(md5(mt_rand().rand().microtime()));
        }

        return substr($token, 0, $length);
    }
}
