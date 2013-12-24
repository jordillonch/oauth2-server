<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    function generate();
}
