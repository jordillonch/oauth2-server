<?php

namespace Akamon\OAuth2\Server\Service\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Service\TokenGenerator\TokenGeneratorInterface;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use felpado as f;

class BearerAccessTokenCreator implements AccessTokenCreatorInterface
{
    private $tokenGenerator;
    private $lifetime;

    public function __construct(TokenGeneratorInterface $tokenGenerator, $lifetime)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->lifetime = $lifetime;
    }

    public function create(Context $context)
    {
        return new AccessToken([
            'token' => $this->tokenGenerator->generate(40),
            'type' => 'bearer',
            'clientId' => f\get($context->getClient(), 'id'),
            'userId' => $context->getUserId(),
            'expiresAt' => time() + $this->lifetime,
            'scope' => $context->getScope()
        ]);
    }
}
