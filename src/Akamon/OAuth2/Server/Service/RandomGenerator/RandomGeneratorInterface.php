<?php

namespace Akamon\OAuth2\Server\Service\RandomGenerator;

interface RandomGeneratorInterface
{
    function generateString($length, $characters);
}
