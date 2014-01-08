<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;

interface TokenGrantTypeProcessorInterface
{
    function process(Client $client, array $inputData);
}
