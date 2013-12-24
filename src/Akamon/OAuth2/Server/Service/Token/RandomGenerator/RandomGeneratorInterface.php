<?php

namespace Akamon\OAuth2\Server\Service\Token\RandomGenerator;

interface RandomGeneratorInterface
{
    function generateString($length, $characters);
}
