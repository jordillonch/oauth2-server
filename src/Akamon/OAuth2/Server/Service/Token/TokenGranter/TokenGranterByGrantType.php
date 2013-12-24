<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGranter;

use Akamon\OAuth2\Server\Exception\OAuthError\GrantTypeNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\InvalidClientCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\UnauthorizedClientForGrantTypeOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\UnsupportedGrantTypeOAuthErrorException;
use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\Client\ClientCredentials;
use Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Service\Client\ClientCredentialsObtainer\ClientCredentialsObtainerInterface;
use Akamon\OAuth2\Server\Service\Client\ClientObtainer\ClientObtainerInterface;
use Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenGranterByGrantType implements TokenGranterInterface
{
    private $clientObtainer;
    private $processors = [];

    public function __construct(ClientObtainerInterface $clientObtainer, array $processors)
    {
        $this->clientObtainer = $clientObtainer;

        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    private function addProcessor(TokenGrantTypeProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    public function grant(Request $request)
    {
        $client = $this->clientObtainer->getClient($request);
        $grantType = $this->getGrantTypeFromRequest($request);

        if (!$client->hasAllowedGrantType($grantType)) {
            throw new UnauthorizedClientForGrantTypeOAuthErrorException();
        }

        return $this->findProcessor($grantType)->process($request);
    }

    private function getGrantTypeFromRequest(Request $request)
    {
        if (!$request->request->has('grant_type')) {
            throw new GrantTypeNotFoundOAuthErrorException();
        }

        return $request->request->get('grant_type');
    }

    /**
     * @return TokenGrantTypeProcessorInterface
     */
    private function findProcessor($grantType)
    {
        foreach ($this->processors as $processor) {
            if ($processor->getGrantType() === $grantType) {
                return $processor;
            }
        }

        throw new UnsupportedGrantTypeOAuthErrorException();
    }
}
