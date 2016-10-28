<?php

namespace Roapp\MediaBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('roapp_media');

        $rootNode
            ->fixXmlConfig('upload')
            ->children()
                ->arrayNode('uploads')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->end()
                            ->scalarNode('directory')->end()
                            ->scalarNode('parallel_uploads')
                                ->defaultValue('1')
                            ->end()
                            ->scalarNode('max_filesize')
                                ->defaultValue('1')
                            ->end()
                            ->scalarNode('max_files')
                                ->defaultValue('1')
                            ->end()
                            ->scalarNode('accepted_files')
                                ->defaultValue(null)
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('temporary_directory')->end()
                ->scalarNode('permanent_directory')->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
