<?php

namespace Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('akamon_oauth2_server');

        $rootNode
            ->children()

                ->scalarNode('token_lifetime')->isRequired()->end()
                ->arrayNode('scopes')
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('repositories')
                    ->children()
                        ->scalarNode('client')->isRequired()->end()
                        ->scalarNode('access_token')->isRequired()->end()
                        ->scalarNode('scope')->end()
                        ->scalarNode('refresh_token')->end()
                    ->end()
                ->end()

                ->arrayNode('token_grant_type_processors')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('id')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }

}
