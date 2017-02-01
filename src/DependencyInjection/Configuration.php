<?php
namespace Dende\MultitenancyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dende_multitenancy');

        $rootNode
            ->children()
                ->arrayNode('patched_commands')->prototype('scalar')->end()
            ->end()
            ->arrayNode('connections')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('command_parameter_name')->end()
                        ->scalarNode('command_parameter_description')->end()
                        ->scalarNode('fixtures_dir')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
