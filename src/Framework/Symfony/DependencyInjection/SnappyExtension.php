<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Core\Frontend;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\BackendConfigurationFactory;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\DompdfConfigurationFactory;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\WkHtmlToPdfConfigurationFactory;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class SnappyExtension extends Extension
{
    private const FRONTENDS = [
        Adapter\DOMDocumentToPdf::class => Frontend\DOMDocumentToPdf::class,
        Adapter\HtmlFileToPdf::class => Frontend\HtmlFileToPdf::class,
        Adapter\HtmlToPdf::class => Frontend\HtmlToPdf::class,
        Adapter\StreamToPdf::class => Frontend\StreamToPdf::class,
    ];

    public function load(array $configuration, ContainerBuilder $container): void
    {
        $configuration = $this->processConfiguration(
            $this->getConfiguration($configuration, $container),
            $configuration
        );

        $factories = array_merge(
            ...array_map(
                static fn (BackendConfigurationFactory $factory): array => [$factory->getKey() => $factory],
                $this->getFactories(),
            ),
        );

        foreach ($configuration['backends'] as $backendName => $subConfiguration) {
            foreach ($subConfiguration as $backendType => $backendConfiguration) {
                $backendId = $this->buildBackendServiceId($backendName);
                $factoryId = $this->buildFactoryServiceId($backendName);
                $options = $this->buildOptions($backendName, $backendType, $backendConfiguration['options']);

                $factories[$backendType]
                    ->create(
                        $container,
                        $backendConfiguration,
                        $backendId,
                        $backendName,
                        $factoryId,
                        $options,
                    )
                ;

                foreach (self::FRONTENDS as $adapterClass => $frontendClass) {
                    $frontendId = $this->buildFrontendServiceId($backendName, $frontendClass);

                    $container
                        ->setDefinition(
                            $frontendId,
                            new Definition(
                                $frontendClass,
                                [
                                    '$adapter' => new Reference($backendId),
                                    '$streamFactory' => new Reference(StreamFactoryInterface::class),
                                ],
                            ),
                        )
                    ;
                }

                $container->registerAliasForArgument($frontendId, $adapterClass, $backendName);
            }
        }
    }

    /**
     * @param array<mixed> $configuration
     */
    public function getConfiguration(array $configuration, ContainerBuilder $container): Configuration
    {
        return new Configuration(...$this->getFactories());
    }

    /**
     * @return array<BackendConfigurationFactory>
     */
    private function getFactories(): array
    {
        return array_filter(
            [
                new DompdfConfigurationFactory(),
                new WkHtmlToPdfConfigurationFactory(),
            ],
            static fn (BackendConfigurationFactory $factory): bool => $factory->isAvailable(),
        );
    }

    /**
     * @return non-empty-string
     */
    private function buildBackendServiceId(string $name): string
    {
        return $this->normalizeId(Adapter::class).'.'.$name;
    }

    /**
     * @return non-empty-string
     */
    private function buildFactoryServiceId(string $name): string
    {
        return $this->normalizeId(Factory::class).'.'.$name;
    }

    /**
     * @param class-string $class
     *
     * @return non-empty-string
     */
    private function buildFrontendServiceId(string $name, string $class): string
    {
        return $this->normalizeId($class).'.'.$name;
    }

    /**
     * @param non-empty-string $id
     *
     * @return non-empty-string
     */
    private function normalizeId(string $id): string
    {
        return strtolower(str_replace('\\', '.', $id));
    }

    /**
     * @param array<mixed> $configuration
     */
    private function buildOptions(string $backendName, string $backendType, array $configuration): Definition
    {
        $arguments = [
            '$pageOrientation' => null,
            '$extraOptions' => [],
        ];

        if (isset($configuration['pageOrientation'])) {
            if (false === \is_string($configuration['pageOrientation'])) {
                throw new InvalidConfigurationException(
                    \sprintf(
                        'Invalid “%s” type for “snappy.backends.%s.%s.options.pageOrientation”. The expected type is “string”.',
                        $backendName,
                        $backendType,
                        \gettype($configuration['pageOrientation'])
                    ),
                );
            }

            $arguments['$pageOrientation'] = match ($configuration['pageOrientation']) {
                strtolower(PageOrientation::Landscape->name) => PageOrientation::Landscape,
                strtolower(PageOrientation::Portrait->name) => PageOrientation::Portrait,
                default => PageOrientation::Portrait,
            };
        }

        if (isset($configuration['extraOptions'])) {
            if (false === \is_array($configuration['extraOptions'])) {
                throw new InvalidConfigurationException(
                    \sprintf(
                        'Invalid “%s” type for “snappy.backends.%s.%s.options.extraOptions”. The expected type is “array”.',
                        $backendName,
                        $backendType,
                        \gettype($configuration['extraOptions'])
                    ),
                );
            }

            $arguments['$extraOptions'] = $configuration['extraOptions'];
        }

        return new Definition(Options::class, $arguments);
    }
}
