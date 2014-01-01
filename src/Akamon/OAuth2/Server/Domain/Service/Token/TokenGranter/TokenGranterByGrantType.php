<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGranter;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\GrantTypeNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\UnauthorizedClientForGrantTypeOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\UnsupportedGrantTypeOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Service\Client\ClientObtainer\ClientObtainerInterface;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\TokenGrantTypeProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use felpado as f;

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

        $inputData = $this->getInputDataFromRequest($request);

        return $this->findProcessor($grantType)->process($client, $inputData);
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

    private function getInputDataFromRequest(Request $request)
    {
        return f\dissoc($request->request->all(), 'grant_type');
    }
}
