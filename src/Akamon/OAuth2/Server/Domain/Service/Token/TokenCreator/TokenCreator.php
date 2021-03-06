<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Token\AccessTokenCreator\AccessTokenCreatorInterface;
use felpado as f;

class TokenCreator implements TokenCreatorInterface
{
    private $accessTokenCreator;

    public function __construct(AccessTokenCreatorInterface $accessTokenCreator)
    {
        $this->accessTokenCreator = $accessTokenCreator;
    }

    public function create(Context $context)
    {
        $accessToken = $this->accessTokenCreator->create($context);

        return [
            'access_token' => f\get($accessToken, 'token'),
            'token_type' => f\get($accessToken, 'type'),
            'expires_in' => f\get($accessToken, 'lifetime'),
            'scope' => f\get($accessToken, 'scope')
        ];
    }
}
