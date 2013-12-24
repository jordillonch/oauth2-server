<?php

namespace Akamon\OAuth2\Server\Service\Token\RandomGenerator;

class ArrayRandRandomGenerator implements RandomGeneratorInterface
{
    public function generateString($length, $characters)
    {
        return $this->generateStringAcc($length, str_split($characters), '');
    }

    private function generateStringAcc($length, $characters, $string)
    {
        if ($length == 0) {
            return $string;
        }

        return $this->generateStringAcc($length - 1, $characters, $string.$this->randomChar($characters));
    }

    private function randomChar($characters)
    {
        return $characters[array_rand($characters)];
    }
}
