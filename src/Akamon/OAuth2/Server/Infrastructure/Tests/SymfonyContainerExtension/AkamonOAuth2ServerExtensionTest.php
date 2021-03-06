<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\SymfonyContainerExtension;

use Akamon\OAuth2\Server\Domain\Tests\OAuth2TestCase;
use Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use felpado as f;
use Symfony\Component\DependencyInjection\Reference;

class AkamonOAuth2ServerExtensionTest extends OAuth2TestCase
{
    /** @var AkamonOAuth2ServerExtension */
    private $extension;
    /** @var ContainerBuilder */
    private $container;

    protected function setUp()
    {
        $this->extension = new AkamonOAuth2ServerExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadTokenLifetime()
    {
        $config = array_merge($this->minimumFakedConfig(), [
            'token_lifetime' => 200
        ]);
        $this->loadExtension($config);

        $this->assertSame(200, $this->container->getParameter('akamon.oauth2_server.token_lifetime'));
    }

    public function testLoadScopes()
    {
        $config = array_merge($this->minimumFakedConfig(), [
            'scopes' => ['foo', 'bar', 'ups']
        ]);
        $this->loadExtension($config);

        $this->assertSame($config['scopes'], $this->container->getParameter('akamon.oauth2_server.scopes'));
    }

    public function testLoadMinimumRepositories()
    {
        $config = array_merge($this->minimumFakedConfig(), [
            'repositories' => [
                'client' => 'client_repository_service',
                'access_token' => 'access_token_repository_service'
            ]
        ]);
        $this->loadExtension($config);

        $this->assertLoadedRepositories($config);
    }

    public function testLoadFullRepositories()
    {
        $config = array_merge($this->minimumFakedConfig(), array(
            'repositories' => [
                'client' => 'client_repository_service',
                'access_token' => 'access_token_repository_service',
                'scope' => 'access_token_repository_service',
                'refresh_token' => 'refresh_token_repository_service'
            ]
        ));
        $this->loadExtension($config);

        $this->assertLoadedRepositories($config);
    }

    private function assertLoadedRepositories($config)
    {
        f\each(function ($service, $what) {
            $this->assertSame($service, (string) $this->container->getAlias(sprintf('akamon.oauth2_server.%s_repository', $what)));
        }, $config['repositories']);
    }

    public function testLoadTokenGrantTypeProcessor()
    {
        $config = array_merge($this->minimumFakedConfig(), [
            'token_grant_type_processors' => [
                'password' => [
                    'id' => 'foo'
                ]
            ]
        ]);
        $this->loadExtension($config);

        $def = $this->container->getDefinition('akamon.oauth2_server.server_builder');

        $this->assertTrue($def->hasMethodCall('addTokenGrantTypeProcessor'));
    }

    private function loadExtension($config)
    {
        $configs = $this->createConfigs($config);
        $this->extension->load($configs, $this->container);
    }

    private function minimumFakedConfig()
    {
        return [
            'token_lifetime' => '123',
            'repositories' => [
                'client' => 'foo',
                'access_token' => 'bar'
            ]
        ];
    }

    private function createConfigs(array $config)
    {
        return ['akamon_oauth2_server' => $config];
    }
}
