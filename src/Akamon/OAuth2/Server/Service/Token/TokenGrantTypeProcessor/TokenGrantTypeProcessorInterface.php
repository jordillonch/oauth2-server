<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Model\Client\Client;

interface TokenGrantTypeProcessorInterface
{
    function getGrantType();

    function process(Client $client, array $inputData);
}
