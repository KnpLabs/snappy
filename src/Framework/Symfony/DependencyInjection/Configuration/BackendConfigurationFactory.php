<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface BackendConfigurationFactory
{
    /**
     * @return non-empty-string
     */
    public function getKey(): string;

    public function isAvailable(): bool;

    /**
     * @return array<mixed>
     */
    public function getExample(): array;

    /**
     * @param array<mixed>     $configuration
     * @param non-empty-string $backendId
     * @param non-empty-string $factoryId
     * @param non-empty-string $backendName
     */
    public function create(
        ContainerBuilder $container,
        array $configuration,
        string $backendId,
        string $backendName,
        string $factoryId,
        Definition $options
    ): void;

    public function addConfiguration(ArrayNodeDefinition $node): void;
}
