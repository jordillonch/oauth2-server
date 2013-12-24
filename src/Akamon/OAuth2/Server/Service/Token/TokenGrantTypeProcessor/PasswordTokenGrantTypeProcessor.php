<?php

namespace Akamon\OAuth2\Server\Service\Token\TokenGrantTypeProcessor;

use Akamon\OAuth2\Server\Exception\OAuthError\InvalidUserCredentialsOAuthErrorException;
use Akamon\OAuth2\Server\Exception\OAuthError\UserCredentialsNotFoundException;
use Akamon\OAuth2\Server\Model\UserCredentials;
use Akamon\OAuth2\Server\Service\ContextObtainer\ContextObtainerInterface;
use Akamon\OAuth2\Server\Service\Token\TokenCreator\TokenCreatorInterface;
use Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\UserCredentialsCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordTokenGrantTypeProcessor implements TokenGrantTypeProcessorInterface
{
    private $contextObtainer;
    private $userCredentialsChecker;
    private $tokenCreator;

    public function __construct(ContextObtainerInterface $contextObtainer, UserCredentialsCheckerInterface $userCredentialsChecker, TokenCreatorInterface $tokenCreator)
    {
        $this->contextObtainer = $contextObtainer;
        $this->userCredentialsChecker = $userCredentialsChecker;
        $this->tokenCreator = $tokenCreator;
    }

    public function getGrantType()
    {
        return 'password';
    }

    public function process(Request $request)
    {
        $getUsername = function () use ($request) {
            return $request->request->get('username');
        };
        $context = $this->contextObtainer->getContext($request, $getUsername);

        $userCredentials = $this->getUserCredentialsFromRequest($request);
        if (!$this->userCredentialsChecker->check($userCredentials)) {
            throw new InvalidUserCredentialsOAuthErrorException();
        }

        return $this->tokenCreator->create($context);
    }

    private function getUserCredentialsFromRequest(Request $request)
    {
        if ($this->hasNoUserCredentialsInRequest($request)) {
            throw new UserCredentialsNotFoundException();
        }

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        return new UserCredentials($username, $password);
    }

    private function hasNoUserCredentialsInRequest(Request $request)
    {
        return !$request->request->has('username') || !$request->request->has('password');
    }
}
