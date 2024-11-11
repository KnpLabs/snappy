<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\WkHtmlToPdfAdapter;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\WkHtmlToPdfFactory;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class WkHtmlToPdfConfigurationFactory implements BackendConfigurationFactory
{
    public function getKey(): string
    {
        return 'wkhtmltopdf';
    }

    public function isAvailable(): bool
    {
        return class_exists(WkHtmlToPdfAdapter::class);
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
                    WkHtmlToPdfFactory::class,
                    [
                        '$streamFactory' => $container->getDefinition(StreamFactoryInterface::class),
                        '$binary' => $configuration['binary'],
                        '$timeout' => $configuration['timeout'],
                    ]
                ),
            )
        ;

        $container
            ->setDefinition(
                $backendId,
                (new Definition(WkHtmlToPdfAdapter::class))
                    ->setFactory([$container->getDefinition($factoryId), 'create'])
                    ->setArgument('$options', $options)
            )
        ;

        $container->registerAliasForArgument($backendId, WkHtmlToPdfAdapter::class, $backendName);
    }

    public function getExample(): array
    {
        return [
            'binary' => '/usr/local/bin/wkhtmltopdf',
        ];
    }

    public function addConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->scalarNode('binary')
            ->defaultValue('wkhtmltopdf')
            ->info('Path or command to run wkdtmltopdf')
        ;

        $node
            ->children()
            ->integerNode('timeout')
            ->defaultValue(60)
            ->min(1)
            ->info('Timeout (seconds) for wkhtmltopdf command')
        ;
    }
}
