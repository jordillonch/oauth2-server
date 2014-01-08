<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Exception\UserNotFoundException;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Scope\ScopesObtainer\ScopesObtainerInterface;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface;
use felpado as f;

class DirectTokenGrantTypeProcessor implements TokenGrantTypeProcessorInterface
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
        $userId = $this->getUserId($inputData);
        $scopes = $this->scopesObtainer->getScopes($inputData);

        $context = new Context($client, $userId, $scopes);

        return $this->tokenCreator->create($context);
    }

    private function getUserId(array $inputData)
    {
        if (f\not($inputData, 'user_id')) {
            throw new UserNotFoundException();
        }

        return f\get($inputData, 'user_id');
    }
}
