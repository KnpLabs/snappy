<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption\PrintToPdf;
use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumAdapter;
use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class HeadlessChromiumConfigurationFactory implements BackendConfigurationFactory
{
    public function getKey(): string
    {
        return 'chromium';
    }

    public function isAvailable(): bool
    {
        return \class_exists(HeadlessChromiumAdapter::class);
    }

    public function create(
        ContainerBuilder $container,
        array $configuration,
        string $backendId,
        string $backendName,
        string $factoryId,
        Definition $options
    ): void {
        $container
            ->setDefinition(
                $factoryId,
                new Definition(
                    HeadlessChromiumFactory::class,
                    [
                        '$streamFactory' => $container->getDefinition(Psr17Factory::class),
                        '$binary' => $configuration['binary'],
                        '$timeout' => $configuration['timeout'],
                    ]
                )
            )
        ;

        $container
            ->setDefinition(
                $backendId,
                (new Definition(HeadlessChromiumAdapter::class))
                    ->setFactory([$container->getDefinition($factoryId), 'create'])
                    ->setArgument('$options', $options)
            )
        ;

        $container->registerAliasForArgument($backendId, HeadlessChromiumAdapter::class, $backendName);
    }

    public function getExample(): array
    {
        return [
            'extraOptions' => [
                'construct' => [],
                'output' => [],
            ],
        ];
    }

    public function addConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->scalarNode('binary')
            ->defaultValue('chromium')
            ->info('Path or command to run Chromium')
        ;

        $node
            ->children()
            ->scalarNode('headless')
            ->defaultValue('--headless')
            ->info('The flag to run Chromium in headless mode')
        ;

        $node
            ->children()
            ->integerNode('timeout')
            ->defaultValue(60)
            ->info('Timeout for Chromium process')
        ;

        $optionsNode = $node
            ->children()
            ->arrayNode('options')
            ->info('Options to configure the Chromium process.')
            ->addDefaultsIfNotSet()
            ->children()
        ;

        $optionsNode
            ->arrayNode('extraOptions')
            ->info('Extra options passed to the HeadlessChromiumAdapter.')
            ->children()
            ->scalarNode('printToPdf')
            ->info(\sprintf('Configuration passed to %s::__construct().', PrintToPdf::class))
        ;
    }
}
