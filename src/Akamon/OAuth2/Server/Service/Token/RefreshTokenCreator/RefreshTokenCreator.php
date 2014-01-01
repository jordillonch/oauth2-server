<?php

namespace Akamon\OAuth2\Server\Service\Token\RefreshTokenCreator;

use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Service\Token\TokenGenerator\TokenGeneratorInterface;
use felpado as f;

class RefreshTokenCreator implements RefreshTokenCreatorInterface
{
    private $tokenGenerator;
    private $lifetime;

    public function __construct(TokenGeneratorInterface $tokenGenerator, $params)
    {
        $this->tokenGenerator = $tokenGenerator;

        f\validate_collection($params, ['lifetime' => f\required(['v' => 'is_int'])]);
        $this->lifetime = $params['lifetime'];
    }

    /**
     * @return \Akamon\OAuth2\Server\Model\RefreshToken\RefreshToken
     */
    public function create($accessTokenTokenToken)
    {
        return new RefreshToken([
            'token' => $this->tokenGenerator->generate(),
            'accessTokenToken' => $accessTokenTokenToken,
            'createdAt' => time(),
            'lifetime' => $this->lifetime
        ]);
    }
}
