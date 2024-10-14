<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration;

use Dompdf\Dompdf;
use KNPLabs\Snappy\Backend\Dompdf\Adapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfAdapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class DompdfConfigurationFactory implements BackendConfigurationFactory
{
    public function getKey(): string
    {
        return 'dompdf';
    }

    public function isAvailable(): bool
    {
        return class_exists(DompdfAdapter::class);
    }

    public function create(ContainerBuilder $container, array $configuration, string $backendId, string $backendName,  string $factoryId, Definition $options): void
    {
        $container
            ->setDefinition(
            $factoryId,
            new Definition(
                DompdfFactory::class,
                [
                    '$streamFactory' => $container->getDefinition(StreamFactoryInterface::class)
                ]
            ),
        );

        $container
            ->setDefinition(
                $backendId,
                (new Definition(DompdfAdapter::class))
                    ->setFactory([$container->getDefinition($factoryId), 'create'])
                    ->setArgument('$options', $options)
            )
        ;

        $container->registerAliasForArgument($backendId, DompdfAdapter::class, $backendName);
    }

    public function getExample(): array
    {
        return [
            'extraOptions' => [
                'construct'   => [],
                'output' => [],
            ]
        ];
    }

    public function addConfiguration(ArrayNodeDefinition $node): void
    {
        $optionsNode = $node
            ->getChildNodeDefinitions()['options']
        ;

        $extraOptionsNode = $optionsNode
            ->getChildNodeDefinitions()['extraOptions']
        ;

        $extraOptionsNode
            ->children()
            ->variableNode('construct')
            ->info(sprintf('Configuration passed to %s::__construct().', Dompdf::class))
        ;

        $extraOptionsNode
            ->children()
            ->variableNode('output')
            ->info(sprintf('Configuration passed to %s::output().', Dompdf::class))
        ;
    }
}
