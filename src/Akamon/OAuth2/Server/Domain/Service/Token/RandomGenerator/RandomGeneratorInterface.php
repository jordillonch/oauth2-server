<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\RandomGenerator;

interface RandomGeneratorInterface
{
    function generateString($length, $characters);
}
