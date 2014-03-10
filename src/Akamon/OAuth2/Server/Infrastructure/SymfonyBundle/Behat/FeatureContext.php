<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyBundle\Behat;

use Akamon\Behat\ApiContext\Domain\ApiContext;
use Akamon\Behat\ApiContext\Domain\Service\ClientRequester\ClientRequesterInterface;
use Akamon\Behat\ApiContext\Domain\Service\Parameter\ParameterAccessor\DeepArrayParameterAccessor;
use Akamon\Behat\ApiContext\Domain\Service\ResponseParametersProcessor\JsonResponseParametersProcessor;
use Akamon\Behat\ApiContext\Infrastructure\ClientRequester\SymfonyHttpKernelClientRequester;
use Akamon\Behat\ApiContext\Infrastructure\RequestConverter\SymfonyHttpFoundationRequestConverter;
use Akamon\Behat\ApiContext\Infrastructure\ResponseConverter\SymfonyHttpFoundationResponseConverter;
use Akamon\OAuth2\Server\Domain\Model\Client\Client;
use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Akamon\Behat\SymfonyKernelContext\SymfonyKernelBehatContext;
use Akamon\OAuth2\Server\Domain\Storage;
use felpado as f;

class FeatureContext extends BehatContext
{
    private $kernelContext;
    private $clientRequester;
    private $apiContext;

    private $kernel;

    /** @var ContainerInterface */
    private $container;

    public function __construct(array $parameters)
    {
        $this->kernelContext = new SymfonyKernelBehatContext();
        $this->clientRequester = $this->createClientRequester();
        $this->apiContext = $this->createApiContext($this->clientRequester);

        $this->useContext('symfony_kernel', $this->kernelContext);
        $this->useContext('api', $this->apiContext);
    }

    private function createClientRequester()
    {
        return new SymfonyHttpKernelClientRequester(
            new SymfonyHttpFoundationRequestConverter(), new SymfonyHttpFoundationResponseConverter()
        );
    }

    private function createApiContext(ClientRequesterInterface $clientRequester)
    {
        return new ApiContext($clientRequester, new DeepArrayParameterAccessor(['separator' => '.']), new JsonResponseParametersProcessor());
    }

    /**
     * @Given /^I use the kernel$/
     */
    public function iUseTheKernel()
    {
        $this->kernel = $this->kernelContext->getKernel();
        $this->container = $this->kernel->getContainer();

        $this->clientRequester->setHttpKernel($this->kernel);
    }

    /**
     * @Given /^there are oauth clients:$/
     */
    public function thereAreOauthClients(TableNode $table)
    {
        $jsonDecode = function ($v) { return json_decode($v) ?: $v; };
        $clients = f\map(f\partial('felpado\map', $jsonDecode), $table->getHash());

        f\each(function ($params) {
            $this->getOAuthClientRepository()->add(new Client($params));
        }, $clients);
    }

    /**
     * @Given /^I add the http basic authentication for the oauth client "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTheHttpBasicAuthenticationForTheOauthClientAnd($id, $secret)
    {
        $this->apiContext->addHttpBasicAuthentication($id, $secret);
    }

    private function getOAuthClientRepository()
    {
        return $this->getStorage()->getClientRepository();
    }

    /**
     * @return Storage
     */
    private function getStorage()
    {
        return $this->container->get('akamon.oauth2_server.storage');
    }
}
