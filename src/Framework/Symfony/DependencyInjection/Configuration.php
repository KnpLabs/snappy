<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection;

use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\BackendConfigurationFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var array<BackendConfigurationFactory>
     */
    private array $factories;

    public function __construct(BackendConfigurationFactory ...$factories)
    {
        $this->factories = $factories;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('snappy');
        $rootNode    = $treeBuilder->getRootNode();

        $backendNodeBuilder = $rootNode
            ->children()
            ->arrayNode('backends')
            ->useAttributeAsKey('name')
            ->example(
                array_merge(
                    ...array_map(
                        fn(BackendConfigurationFactory $factory): array => [
                            $factory->getKey() => [
                                'pageOrientation' => PageOrientation::PORTRAIT->value,
                                'options' => [],
                                ...$factory->getExample()
                            ],
                        ],
                        $this->factories
                    )
                )
            )
            ->arrayPrototype()
        ;

        foreach ($this->factories as $factory) {
            $name = str_replace('-', '_', $factory->getKey());

            $factoryNode = $backendNodeBuilder
                ->children()
                ->arrayNode($name)
                ->canBeUnset()
            ;

            $this->buildOptionsConfiguration($factoryNode);

            $factory->addConfiguration($factoryNode);
        }

        return $treeBuilder;
    }

    private function buildOptionsConfiguration(ArrayNodeDefinition $node): void
    {
        $optionsNode = $node
            ->children()
            ->arrayNode('options')
        ;

        $optionsNode
            ->children()
            ->enumNode('pageOrientation')
            ->values(
                array_map(
                    fn(PageOrientation $pageOrientation): string => $pageOrientation->value,
                    PageOrientation::cases(),
                )
            )
        ;

        $optionsNode
            ->children()
            ->arrayNode('extraOptions')
        ;
    }
}
