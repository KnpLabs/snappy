<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration;

use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessAdapter;
use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessFactory;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ChromeHeadlessConfigurationFactory implements BackendConfigurationFactory
{
    public function getKey(): string
    {
        return 'chrome_headless';
    }

    public function isAvailable(): bool
    {
        return class_exists(ChromeHeadlessAdapter::class);
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
                    ChromeHeadlessFactory::class,
                    [
                        '$binary' => $configuration['binary'],
                        '$timeout' => $configuration['timeout'],
                        '$streamFactory' => $container->getDefinition(StreamFactoryInterface::class),
                        '$uriFactory' => $container->getDefinition(UriFactoryInterface::class),
                    ]
                ),
            )
        ;

        $container
            ->setDefinition(
                $backendId,
                (new Definition(ChromeHeadlessAdapter::class))
                    ->setFactory([$container->getDefinition($factoryId), 'create'])
                    ->setArgument('$options', $options)
            )
        ;

        $container->registerAliasForArgument($backendId, ChromeHeadlessAdapter::class, $backendName);
    }

    public function getExample(): array
    {
        return [
            'binary' => '/usr/bin/google-chrome',
        ];
    }

    public function addConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->scalarNode('binary')
            ->defaultValue('google-chrome')
            ->info('Path or command to run Google Chrome')
        ;

        $node
            ->children()
            ->integerNode('timeout')
            ->defaultValue(60)
            ->min(1)
            ->info('Timeout (seconds) for Chrome command')
        ;
    }
}
