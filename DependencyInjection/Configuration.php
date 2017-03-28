<?php

namespace Padam87\AccountBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('padam87_account');

        $rootNode
            ->children()
                ->arrayNode('classes')
                    ->children()
                        ->scalarNode('account')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('transaction')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('currencies')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->booleanNode('registration_listener')
                    ->defaultFalse()
                ->end()
                ->arrayNode('accountant')
                    ->children()
                        ->scalarNode('class')->defaultNull()->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
