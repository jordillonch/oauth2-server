<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use felpado as f;
use Symfony\Component\DependencyInjection\Reference;

class AkamonOAuth2ServerExtension extends Extension
{
    public function getAlias()
    {
        return 'akamon_oauth2_server';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Resources'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('akamon.oauth2_server.token_lifetime', $config['token_lifetime']);
        $container->setParameter('akamon.oauth2_server.scopes', $config['scopes']);
        $this->loadRepositories($config['repositories'], $container);

        if (f\contains($config, 'token_grant_type_processors')) {
            $this->loadTokenGrantTypeProcessors($config['token_grant_type_processors'], $container);
        }
    }

    private function loadRepositories(array $configRepositories, ContainerBuilder $container)
    {
        f\each(function ($service, $what) use ($container) {
            $container->setAlias(sprintf('akamon.oauth2_server.%s_repository', $what), $service);
        }, $configRepositories);
    }

    private function loadTokenGrantTypeProcessors(array $config, ContainerBuilder $container)
    {
        $builderDef = $container->getDefinition('akamon.oauth2_server.server_builder');

        f\each(function ($id, $name) use ($builderDef) {
            $builderDef->addMethodCall('addTokenGrantTypeProcessor', [$name, new Reference($id)]);
        }, f\map(f\key('id'), $config));
    }
}
