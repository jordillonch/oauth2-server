<?php

namespace Akamon\OAuth2\Server\Tests\Behat;


use Akamon\OAuth2\Server\Exception\UserNotFoundException;
use Akamon\OAuth2\Server\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Model\Client\Client;
use Akamon\OAuth2\Server\Model\UserCredentials;
use Akamon\OAuth2\Server\Service\User\UserCredentialsChecker\CallbackUserCredentialsChecker;
use Behat\Behat\Context\BehatContext;
use Akamon\Behat\ApiContext\ApiContext;
use Akamon\Behat\ApiContext\ParameterAccessor\DeepArrayParameterAccessor;
use Akamon\Behat\ApiContext\ResponseParametersProcessor\JsonResponseParametersProcessor;
use Akamon\OAuth2\Server\Model\AccessToken\Infrastructure\DoctrineCacheAccessTokenRepository;
use Akamon\OAuth2\Server\Model\Client\Infrastructure\FileClientRepository;
use Akamon\OAuth2\Server\Model\RefreshToken\Infrastructure\DoctrineCacheRefreshTokenRepository;
use Akamon\OAuth2\Server\OAuth2ServerBuilder;
use Akamon\OAuth2\Server\Service\User\UserIdObtainer\CallbackUserIdObtainer;
use Akamon\OAuth2\Server\Storage;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Cache\ArrayCache;
use Akamon\OAuth2\Server\Model\Client\ClientRepositoryInterface;
use Akamon\OAuth2\Server\Model\AccessToken\AccessTokenRepositoryInterface;
use Akamon\OAuth2\Server\OAuth2Server;
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
        $refreshTokenRepository = new DoctrineCacheRefreshTokenRepository(new ArrayCache());

        $storage = new Storage($this->clientRepository, $this->accessTokenRepository, $refreshTokenRepository);
        $resourceProcessor = [$this, 'resourceProcessor'];

        $builder = new OAuth2ServerBuilder($storage, $resourceProcessor);
        $builder->addResourceOwnerPasswordCredentialsGrant($this->createUserCredentialsChecker($this->users), $this->createUserIdObtainer($this->users));

        $this->server = $builder->build();
    }

    public function resourceProcessor(Request $request, AccessToken $accessToken)
    {
        $response = new Response();
        $response->headers->set('content-type', 'text/plain');
        $response->setContent('My resource!');

        return $response;
    }

    private function createUserIdObtainer($users)
    {
        $getId = function ($username) use ($users) {
            $isUser = function ($user) use ($username) {
                return $user['username'] === $username;
            };

            $user = f\find($isUser, $users);
            if ($user) {
                return $user['id'];
            };

            throw new UserNotFoundException();
        };

        return new CallbackUserIdObtainer($getId);
    }

    private function createUserCredentialsChecker($users)
    {
        $check = function (UserCredentials $userCredentials) use ($users) {
            $isUser = function ($user) use ($userCredentials) {
                return $user['username'] === $userCredentials->getUsername();
            };

            $user = f\find($isUser, $users);
            if ($user) {
                return $user['password'] === $userCredentials->getPassword();
            };

            return false;
        };

        return new CallbackUserCredentialsChecker($check);
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
     * @Given /^I have an expired access token "([^"]*)"$/
     */
    public function iHaveAnExpiredAccessToken($token)
    {
        $accessToken = $this->createAccessToken([
            'token' => $token,
            'createdAt' => time() - 60,
            'lifetime' => 59
        ]);

        $this->accessTokenRepository->add($accessToken);
    }

    /**
     * @Given /^I have a valid access token "([^"]*)"$/
     */
    public function iHaveAValidAccessToken($token)
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
