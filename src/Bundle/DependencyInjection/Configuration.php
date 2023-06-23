<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
      $treeBuilder = new TreeBuilder('snappy');

      $treeBuilder->getRootNode()
        ->children()
          ->arrayNode('backends')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
              ->children()
                  ->scalarNode('driver')
                    ->isRequired()
                    ->validate()
                        ->ifNotInArray(['wkhtmltopdf', 'chromium'])
                        ->thenInvalid('Invalid backend driver %s')
                    ->end()
                  ->end()
                  ->integerNode('timeout')
                    ->min(1)
                    ->defaultValue(30)
                  ->end()
                  ->scalarNode('binary_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                  ->end()
                  ->arrayNode('options')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                  ->end()
              ->end()
            ->end()
          ->end()
        ->end()
      ;

      return $treeBuilder;
    }
}
