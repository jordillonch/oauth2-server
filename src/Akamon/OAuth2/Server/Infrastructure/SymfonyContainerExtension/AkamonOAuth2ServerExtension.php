<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use felpado as f;

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
        $this->loadRepositories($config['repositories'], $container);
    }

    private function loadRepositories(array $configRepositories, ContainerBuilder $container)
    {
        f\each(function ($service, $what) use ($container) {
            $container->setAlias(sprintf('akamon.oauth2_server.%s_repository', $what), $service);
        }, $configRepositories);
    }
}
