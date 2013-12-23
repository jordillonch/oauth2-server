<?php

namespace Akamon\OAuth2\Server\Service\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    function generate($length);
}
