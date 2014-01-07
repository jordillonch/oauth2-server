<?php

namespace Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Domain\Exception\OAuthError\ExpiredRefreshTokenOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\InvalidRefreshTokenOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Exception\OAuthError\RefreshTokenNotFoundOAuthErrorException;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\Context;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeCollection;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenCreator\TokenCreatorInterface;
use felpado as f;

class RefreshTokenGrantTypeProcessor implements TokenGrantTypeProcessorInterface
{
    private $refreshTokenRepository;
    private $accessTokenRepository;
    private $tokenCreator;

    public function __construct(RefreshTokenRepositoryInterface $refreshTokenRepository, AccessTokenRepositoryInterface $accessTokenRepository, TokenCreatorInterface $tokenCreator)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->tokenCreator = $tokenCreator;
    }

    public function getGrantType()
    {
        return 'refresh_token';
    }

    public function process(Client $client, array $inputData)
    {
        $context = $this->getContext($client, $inputData);

        return $this->tokenCreator->create($context);
    }

    private function getContext(Client $client, array $inputData)
    {
        if (f\not(f\contains($inputData, 'refresh_token'))) {
            throw new RefreshTokenNotFoundOAuthErrorException();
        }

        $refreshToken = $this->refreshTokenRepository->find(f\get($inputData, 'refresh_token'));
        if (f\not($refreshToken)) {
            throw new InvalidRefreshTokenOAuthErrorException();
        }

        if ($refreshToken->isExpired()) {
            throw new ExpiredRefreshTokenOAuthErrorException();
        }

        $accessToken = $this->accessTokenRepository->find(f\get($refreshToken, 'accessTokenToken'));
        if (f\not($accessToken)) {
            $this->refreshTokenRepository->remove($refreshToken);

            throw new InvalidRefreshTokenOAuthErrorException();
        }

        $this->refreshTokenRepository->remove($refreshToken);
        $this->accessTokenRepository->remove($accessToken);

        return new Context($client, f\get($accessToken, 'userId'), ScopeCollection::createFromString(f\get($accessToken, 'scope')));
    }
}
