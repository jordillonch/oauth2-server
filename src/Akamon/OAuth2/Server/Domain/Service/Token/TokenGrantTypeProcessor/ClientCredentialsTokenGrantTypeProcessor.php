<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface;
use felpado as f;

class ClientCredentialsTokenGrantTypeProcessor implements TokenGrantTypeProcessorInterface
{
    private $scopesObtainer;
    private $tokenCreator;

    public function __construct(ScopesObtainerInterface $scopesObtainer, TokenCreatorInterface $tokenCreator)
    {
        $this->scopesObtainer = $scopesObtainer;
        $this->tokenCreator = $tokenCreator;
    }

    public function process(Client $client, array $inputData)
    {
        $userId = f\get($client, 'id');
        $scopes = $this->scopesObtainer->getScopes($inputData);

        $context = new Context($client, $userId, $scopes);

        return $this->tokenCreator->create($context);
    }
}
