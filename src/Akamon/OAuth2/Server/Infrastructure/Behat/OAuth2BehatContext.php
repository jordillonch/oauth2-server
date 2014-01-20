<?php

namespace Akamon\OAuth2\Server\Infrastructure\Behat;

use Akamon\Behat\ApiContext\Domain\ApiContext;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Domain\Storage;
use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use felpado as f;

class OAuth2BehatContext extends BehatContext
{
    private $storage;
    private $apiContext;

    public function __construct(Storage $storage, ApiContext $apiContext = null)
    {
        $this->storage = $storage;
        $this->apiContext = $apiContext;
    }

    /**
     * @Given /^there are oauth2 clients:$/
     */
    public function thereAreOauthClients(TableNode $table)
    {
        $jsonDecode = function ($v) { return json_decode($v) ?: $v; };
        $clients = f\map(f\partial('felpado\map', $jsonDecode), $table->getHash());

        f\each([$this, 'createOAuthClient'], $clients);
    }

    public function createOAuthClient($params)
    {
        $this->storage->getClientRepository()->add(new Client($params));
    }

    /**
     * @Given /^I add the http basic authentication for the oauth2 client "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTheHttpBasicAuthenticationForTheOauthClientAnd($name, $secret)
    {
        $client = $this->findClientByName($name);

        $this->apiContext->addHttpBasicAuthentication(f\get($client, 'id'), $secret);
    }

    private function findClientByName($name)
    {
        $clients = $this->storage->getClientRepository()->findAll();

        foreach ($clients as $client) {
            if (f\get($client, 'name') === $name) {
                return $client;
            }
        }

        throw new \Exception(sprintf('The client "%s" does not exist.', $name));
    }

    /**
     * @Given /^there is an expired oauth2 access token "([^"]*)"$/
     */
    public function thereIsAnExpiredAccessToken($token)
    {
        $accessToken = $this->createAccessToken([
            'token' => $token,
            'createdAt' => time() - 60,
            'lifetime' => 59
        ]);

        $this->storage->getAccessTokenRepository()->add($accessToken);
    }

    /**
     * @Given /^there is a valid oauth2 access token "([^"]*)"$/
     */
    public function thereIsAValidAccessToken($token)
    {
        $accessToken = $this->createAccessToken(['token' => $token]);

        $this->storage->getAccessTokenRepository()->add($accessToken);
    }

    private function createAccessToken(array $params = array())
    {
        return new AccessToken(array_replace([
            'token' => sha1(microtime().mt_rand()),
            'type' => 'bearer',
            'clientId' => mt_rand(),
            'userId' => mt_rand(),
            'lifetime' => 3600
        ], $params));
    }

    /**
     * @Given /^there is a valid oauth2 refresh token "([^"]*)" for the access token "([^"]*)"$/
     */
    public function thereIsAValidRefreshTokenForTheAccessToken($refreshTokenToken, $accessTokenToken)
    {
        $refreshToken = new RefreshToken([
            'token' => $refreshTokenToken,
            'accessTokenToken' => $accessTokenToken,
            'createdAt' => time(),
            'lifetime' => 3600
        ]);

        $this->storage->getRefreshTokenRepository()->add($refreshToken);
    }

    /**
     * @Given /^there is an expired oauth2 refresh token "([^"]*)" for the access token "([^"]*)"$/
     */
    public function thereIsAnExpiredRefreshTokenForTheAccessToken($refreshTokenToken, $accessTokenToken)
    {
        $refreshToken = new RefreshToken([
            'token' => $refreshTokenToken,
            'accessTokenToken' => $accessTokenToken,
            'createdAt' => time() - 3601,
            'lifetime' => 3600
        ]);

        $this->storage->getRefreshTokenRepository()->add($refreshToken);
    }

    /**
     * @Then /^the response should have the oauth2 right format and cache headers$/
     */
    public function theOauthResponseFormatAndCacheAreRight()
    {
        $expectedHeaders = [
            'content-type' => 'application/json',
            'cache-control' => 'no-store, private',
            'pragma' => 'no-cache'
        ];

        foreach ($expectedHeaders as $name => $value) {
            $this->apiContext->checkResponseHeader($name, $value);
        }
    }
}
