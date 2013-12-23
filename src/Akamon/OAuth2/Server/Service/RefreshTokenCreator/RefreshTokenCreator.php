<?php

namespace Akamon\OAuth2\Server\Service\RefreshTokenCreator;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Service\TokenGenerator\TokenGeneratorInterface;
use felpado as f;

class RefreshTokenCreator implements RefreshTokenCreatorInterface
{
    private $tokenGenerator;
    private $lifetime;

    public function __construct(TokenGeneratorInterface $tokenGenerator, $lifetime)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->lifetime = $lifetime;
    }

    /**
     * @return \Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken
     */
    public function create(AccessToken $accessToken)
    {
        return new RefreshToken([
            'token' => $this->tokenGenerator->generate(40),
            'accessTokenToken' => f\get($accessToken, 'token'),
            'expiresAt' => time() + $this->lifetime
        ]);
    }
}
