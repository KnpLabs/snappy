<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Framework\Symfony\DependencyInjection;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption\PrintToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\BackendConfigurationFactory;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\DompdfConfigurationFactory;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\HeadlessChromiumConfigurationFactory;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\Configuration\WkHtmlToPdfConfigurationFactory;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SnappyExtension extends Extension
{
    public function load(array $configuration, ContainerBuilder $container): void
    {
        $configuration = $this->processConfiguration(
            $this->getConfiguration($configuration, $container),
            $configuration
        );

        $factories = \array_merge(
            ...\array_map(
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
        return \array_filter(
            [
                new DompdfConfigurationFactory(),
                new WkHtmlToPdfConfigurationFactory(),
                new HeadlessChromiumConfigurationFactory(),
            ],
            static fn (BackendConfigurationFactory $factory): bool => $factory->isAvailable(),
        );
    }

    /**
     * @return non-empty-string
     */
    private function buildBackendServiceId(string $name): string
    {
        return "snappy.backend.{$name}";
    }

    /**
     * @return non-empty-string
     */
    private function buildFactoryServiceId(string $name): string
    {
        return "snappy.backend.{$name}.factory";
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
            if (!\is_string($configuration['pageOrientation'])) {
                throw new InvalidConfigurationException(\sprintf('Invalid type for “snappy.backends.%s.%s.options.pageOrientation”. Expected "string", got "%s".', $backendName, $backendType, \gettype($configuration['pageOrientation'])));
            }
            $arguments['$pageOrientation'] = PageOrientation::from($configuration['pageOrientation']);
        }

        if (isset($configuration['extraOptions'])) {
            if (!\is_array($configuration['extraOptions'])) {
                throw new InvalidConfigurationException(\sprintf('Invalid type for “snappy.backends.%s.%s.options.extraOptions”. Expected "array", got "%s".', $backendName, $backendType, \gettype($configuration['extraOptions'])));
            }

            foreach ($configuration['extraOptions'] as $key => $value) {
                switch ($key) {
                    case 'printToPdf':
                        if (\is_string($value)) {
                            $arguments['$extraOptions'][] = new PrintToPdf($value);
                        } else {
                            throw new InvalidConfigurationException(\sprintf('Invalid type for “snappy.backends.%s.%s.options.extraOptions.printToPdf”. Expected "string", got "%s".', $backendName, $backendType, \gettype($value)));
                        }

                        break;

                    default:
                        $arguments['$extraOptions'][$key] = $value;

                        break;
                }
            }
        }

        return new Definition(Options::class, $arguments);
    }
}
