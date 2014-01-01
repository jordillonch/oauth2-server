<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator;

use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Service\Token\RefreshTokenCreator\RefreshTokenCreatorInterface;
use felpado as f;

class RefreshingTokenCreator implements TokenCreatorInterface
{
    private $delegate;
    private $refreshTokenCreator;

    public function __construct(TokenCreatorInterface $delegate, RefreshTokenCreatorInterface $refreshTokenCreator)
    {
        $this->delegate = $delegate;
        $this->refreshTokenCreator = $refreshTokenCreator;
    }

    /**
     * @return array An array of parameters.
     */
    public function create(Context $context)
    {
        $params = $this->delegate->create($context);

        $refreshToken = $this->refreshTokenCreator->create(f\get($params, 'access_token'));

        return f\assoc($params, 'refresh_token', f\get($refreshToken, 'token'));
    }
}
