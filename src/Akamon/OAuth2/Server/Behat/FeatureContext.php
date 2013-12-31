<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshToken;
use Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker;
use Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer;
use Behat\Behat\Context\BehatContext;
use Akamon\Behat\ApiContext\ApiContext;
use Akamon\Behat\ApiContext\ParameterAccessor\DeepArrayParameterAccessor;
use Akamon\Behat\ApiContext\ResponseParametersProcessor\JsonResponseParametersProcessor;
use Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository;
use Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository;
use Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheRefreshTokenRepository;
use Akamon\OAuth2\Server\Domain\OAuth2ServerBuilder;
use Akamon\OAuth2\Server\Domain\Storage;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Cache\ArrayCache;
use Akamon\OAuth2\Server\Domain\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Model\RefreshToken\RefreshTokenRepositoryInterface;
use Akamon\OAuth2\Server\Domain\OAuth2Server;
use felpado as f;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureContext extends BehatContext
{
    private $users;
    /** @var ClientRepositoryInterface */
    private $clientRepository;
    /** @var AccessTokenRepositoryInterface */
    private $accessTokenRepository;
    /** @var RefreshTokenRepositoryInterface */
    private $refreshTokenRepository;
    /** @var OAuth2Server */
    private $server;

    /** @var OAuth2Client */
    private $client;

    public function __construct()
    {
        $this->createServer();

        $this->client = new OAuth2Client($this->server);
        $this->useContext('api', $this->createApiContext());
    }

    private function createServer()
    {
        $this->users = new \ArrayObject();
        $this->clientRepository = new FileClientRepository(tempnam(sys_get_temp_dir(), 'akamon-oauth2-server-clients'));

        $this->accessTokenRepository = new DoctrineCacheAccessTokenRepository(new ArrayCache());
        $this->refreshTokenRepository = new DoctrineCacheRefreshTokenRepository(new ArrayCache());

        $storage = new Storage($this->clientRepository, $this->accessTokenRepository, $this->refreshTokenRepository);

        $lifetime = 3600;
        $resourceProcessor = [$this, 'resourceProcessor'];

        $builder = new OAuth2ServerBuilder($storage, ['lifetime' => $lifetime, 'resource_processor' => $resourceProcessor]);

        $userCredentialsChecker = new IterableUserCredentialsChecker($this->users);
        $userIdObtainer = new IterableUserIdObtainer($this->users);
        $builder->addResourceOwnerPasswordCredentialsGrantType($userCredentialsChecker, $userIdObtainer);
        $builder->addRefreshTokenGrantType();

        $this->server = $builder->build();
    }

    public function resourceProcessor(Request $request, AccessToken $accessToken)
    {
        $response = new Response();
        $response->headers->set('content-type', 'text/plain');
        $response->setContent('My resource!');

        return $response;
    }

    private function createApiContext()
    {
        $apiContext = new ApiContext(
            $this->client,
            new DeepArrayParameterAccessor('.'),
            new JsonResponseParametersProcessor()
        );

        return $apiContext;
    }

    /** @return ApiContext */
    public function getApiContext()
    {
        return $this->getSubContext('api');
    }

    public function request($method, $uri)
    {
        $this->getApiContext()->request($method, $uri);
    }

    /**
     * @beforeScenario
     */
    public function removeAllClients()
    {
        f\each([$this->clientRepository, 'remove'], $this->clientRepository->findAll());
    }

    private function findClientByName($name)
    {
        $clients = $this->clientRepository->findAll();

        foreach ($clients as $client) {
            if (f\get($client, 'name') === $name) {
                return $client;
            }
        }

        throw new \Exception(sprintf('The client "%s" does not exist.', $name));
    }

    /**
     * @Given /^there are clients:$/
     */
    public function thereAreOauthClients(TableNode $table)
    {
        $jsonDecode = function ($v) { return json_decode($v) ?: $v; };
        $clients = f\map(f\partial('felpado\map', $jsonDecode), $table->getHash());

        f\each([$this, 'createOAuthClient'], $clients);
    }

    public function createOAuthClient($params)
    {
        $this->clientRepository->add(new Client($params));
    }

    /**
     * @Given /^there is a user "([^"]*)" with password "([^"]*)"$/
     */
    public function thereIsAUserWithPassword($username, $password)
    {
        $this->users[] = [
            'id' => count($this->users) ? f\last(f\keys($this->users)) : 1,
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * @Given /^I add the http basic authentication for the client "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTheHttpBasicAuthenticationForTheOauthClientAnd($name, $secret)
    {
        $client = $this->findClientByName($name);

        $this->iAddTheHttpBasicAuthenticationHeaderWithAnd(f\get($client, 'id'), $secret);
    }

    /**
     * @Given /^there is an expired access token "([^"]*)"$/
     */
    public function thereIsAnExpiredAccessToken($token)
    {
        $accessToken = $this->createAccessToken([
            'token' => $token,
            'createdAt' => time() - 60,
            'lifetime' => 59
        ]);

        $this->accessTokenRepository->add($accessToken);
    }

    /**
     * @Given /^there is a valid access token "([^"]*)"$/
     */
    public function thereIsAValidAccessToken($token)
    {
        $accessToken = $this->createAccessToken(['token' => $token]);

        $this->accessTokenRepository->add($accessToken);
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
     * @Given /^there is a valid refresh token "([^"]*)" for the access token "([^"]*)"$/
     */
    public function thereIsAValidRefreshTokenForTheAccessToken($refreshTokenToken, $accessTokenToken)
    {
        $refreshToken = new RefreshToken([
            'token' => $refreshTokenToken,
            'accessTokenToken' => $accessTokenToken,
            'createdAt' => time(),
            'lifetime' => 3600
        ]);

        $this->refreshTokenRepository->add($refreshToken);
    }

    /**
     * @Given /^there is an expired refresh token "([^"]*)" for the access token "([^"]*)"$/
     */
    public function thereIsAnExpiredRefreshTokenForTheAccessToken($refreshTokenToken, $accessTokenToken)
    {
        $refreshToken = new RefreshToken([
            'token' => $refreshTokenToken,
            'accessTokenToken' => $accessTokenToken,
            'createdAt' => time() - 3601,
            'lifetime' => 3600
        ]);

        $this->refreshTokenRepository->add($refreshToken);
    }

    /**
     * @When /^I add the http basic authentication header with "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTheHttpBasicAuthenticationHeaderWithAnd($username, $password)
    {
        $this->getApiContext()->addRequestHeader('AUTHORIZATION', 'Basic ' . base64_encode($username . ':' . $password));
    }

    /**
     * @When /^I make a token request$/
     */
    public function iMakeAOauthTokenRequest()
    {
        $this->getApiContext()->request('POST', '/oauth/token');
    }

    /**
     * @When /^I make a resource request$/
     */
    public function iMakeAResourceRequest()
    {
        $this->getApiContext()->request('GET', '/resource');
    }

    /**
     * @Then /^the oauth response format and cache are right$/
     */
    public function theOauthResponseFormatAndCacheAreRight()
    {
        $apiContext = $this->getApiContext();

        $expectedHeaders = [
            'content-type' => 'application/json',
            'cache-control' => 'no-store, private',
            'pragma' => 'no-cache'
        ];

        foreach ($expectedHeaders as $name => $value) {
            $apiContext->checkResponseHeader($name, $value);
        }
    }
}
