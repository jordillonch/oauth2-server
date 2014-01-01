<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    function generate();
}
