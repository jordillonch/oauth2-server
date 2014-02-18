<?php

namespace Akamon\OAuth2\Server\Behat;

use Akamon\Behat\ApiContext\Domain\ApiContext;
use Akamon\Behat\ApiContext\Domain\Service\Parameter\ParameterAccessor\DeepArrayParameterAccessor;
use Akamon\Behat\ApiContext\Domain\Service\ResponseParametersProcessor\JsonResponseParametersProcessor;
use Akamon\OAuth2\Server\Domain\Model\AccessToken\AccessToken;
use Akamon\OAuth2\Server\Domain\Model\Scope\Scope;
use Akamon\OAuth2\Server\Domain\Model\Scope\ScopeRepositoryInterface;
use Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\DirectTokenGrantTypeProcessor;
use Akamon\OAuth2\Server\Infrastructure\Behat\OAuth2BehatContext;
use Akamon\OAuth2\Server\Infrastructure\Memory\MemoryScopeRepository;
use Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker;
use Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer;
use Behat\Behat\Context\BehatContext;
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
    private $apiContext;

    private $users;

    /** @var ClientRepositoryInterface */
    private $clientRepository;
    /** @var AccessTokenRepositoryInterface */
    private $accessTokenRepository;
    /** @var RefreshTokenRepositoryInterface */
    private $refreshTokenRepository;
    /** @var ScopeRepositoryInterface */
    private $scopeRepository;

    /** @var Storage */
    private $storage;

    /** @var OAuth2ServerBuilder */
    private $serverBuilder;
    /** @var OAuth2Server */
    private $server;

    /** @var OAuth2Client */
    private $client;

    public function __construct()
    {
        $this->client = new OAuth2Client();
        $this->apiContext = $this->createApiContext();
        $this->useContext('api', $this->apiContext);

        $this->users = new \ArrayObject();
        $this->createRepositories();
        $this->storage = $this->createStorage();
        $this->useContext('oauth2', new OAuth2BehatContext($this->storage, $this->apiContext));
    }

    private function createRepositories()
    {
        $this->clientRepository = new FileClientRepository(tempnam(sys_get_temp_dir(), 'akamon-oauth2-server-clients'));
        $this->accessTokenRepository = new DoctrineCacheAccessTokenRepository(new ArrayCache());
        $this->refreshTokenRepository = new DoctrineCacheRefreshTokenRepository(new ArrayCache());
        $this->scopeRepository = new MemoryScopeRepository();
    }

    private function createStorage()
    {
        return new Storage($this->clientRepository, $this->accessTokenRepository, $this->scopeRepository, $this->refreshTokenRepository);
    }

    private function getServer()
    {
        if (is_null($this->server)) {
            $this->server = $this->createServer();
        }

        return $this->server;
    }

    private function getServerBuilder()
    {
        if (f\not(is_null($this->server))) {
            throw new \LogicException('The server is already built.');
        }

        if (is_null($this->serverBuilder)) {
            $this->serverBuilder = $this->createServerBuilder();
        }

        return $this->serverBuilder;
    }

    private function createServerBuilder()
    {
        $storage = $this->createStorage();

        $lifetime = 3600;
        $resourceProcessor = [$this, 'resourceProcessor'];

        $builder = new OAuth2ServerBuilder($storage, ['lifetime' => $lifetime, 'resource_processor' => $resourceProcessor]);

        return $builder;
    }

    private function createServer()
    {
        $builder = $this->getServerBuilder();

        $userCredentialsChecker = new IterableUserCredentialsChecker($this->users);
        $userIdObtainer = new IterableUserIdObtainer($this->users);

        $builder->addPasswordGrantType($userCredentialsChecker, $userIdObtainer);
        $builder->addClientCredentialsGrantType();
        $builder->addRefreshTokenGrantType();

        return $builder->build();
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
            new DeepArrayParameterAccessor(['separator' => '.']),
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
        $this->client->setServer($this->getServer());
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
     * @Given /^the server has the direct grant type processor$/
     */
    public function theServerHasTheDirectGrantTypeProcessor()
    {
        $builder = $this->getServerBuilder();
        $processor = new DirectTokenGrantTypeProcessor($builder->getScopesObtainer(), $builder->getTokenCreator());

        $builder->addTokenGrantTypeProcessor('direct', $processor);
    }

    /**
     * @When /^I try to grant a token with the client "([^"]*)" and the user id "([^"]*)" and no scope$/
     */
    public function iTryToGrantATokenWithTheClientAndTheUserIdAndNoScope($clientName, $userId)
    {
        $this->iTryToGrantATokenWithTheClientAndTheUserIdAndTheScope($clientName, $userId, null);
    }

    /**
     * @When /^I try to grant a token with the client "([^"]*)" and the user id "([^"]*)" and the scope "([^"]*)"$/
     */
    public function iTryToGrantATokenWithTheClientAndTheUserIdAndTheScope($clientName, $userId, $scope)
    {
        $client = $this->findClientByName($clientName);
        $this->apiContext->addHttpBasicAuthentication(f\get($client, 'id'), f\get($client, 'secret'));

        $inputData = ['grant_type' => 'direct', 'user_id' => $userId];
        if (f\not(is_null($scope))) {
            $inputData = f\assoc($inputData, 'scope', $scope);
        }

        f\each(function ($v, $k) {
            $this->getApiContext()->addRequestParameter($k, $v);
        }, $inputData);

        $this->iMakeAOauthTokenRequest();
    }

    /**
     * @Given /^there are scopes:$/
     */
    public function thereAreScopes(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            $this->scopeRepository->add(new Scope($row[0]));
        }
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
     * @When /^I make a token request$/
     */
    public function iMakeAOauthTokenRequest()
    {
        $this->request('POST', '/oauth/token');
    }

    /**
     * @When /^I make a resource request$/
     */
    public function iMakeAResourceRequest()
    {
        $this->request('GET', '/resource');
    }
}
