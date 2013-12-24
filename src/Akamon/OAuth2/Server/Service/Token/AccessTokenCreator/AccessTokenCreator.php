<?php

namespace Akamon\OAuth2\Server\Service\Token\AccessTokenCreator;

use Akamon\OAuth2\Server\Model\Context;
use Akamon\OAuth2\Server\Service\Token\TokenGenerator\TokenGeneratorInterface;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use felpado as f;

class AccessTokenCreator implements AccessTokenCreatorInterface
{
    private $tokenGenerator;
    private $type;
    private $lifetime;

    public function __construct(TokenGeneratorInterface $tokenGenerator, $params)
    {
        $this->tokenGenerator = $tokenGenerator;

        f\validate_collection($params, [
            'type' => f\required(['v' => 'is_string']),
            'lifetime' => f\required(['v' => 'is_int'])
        ]);

        $this->type = $params['type'];
        $this->lifetime = $params['lifetime'];
    }

    public function create(Context $context)
    {
        return new AccessToken([
            'token' => $this->tokenGenerator->generate(),
            'type' => 'bearer',
            'clientId' => f\get($context->getClient(), 'id'),
            'userId' => $context->getUserId(),
            'lifetime' => $this->lifetime,
            'scope' => $context->getScope()
        ]);
    }
}
