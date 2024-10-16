<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Framework\Symfony\Tests\DependencyInjection;

use KNPLabs\Snappy\Backend\Dompdf\DompdfAdapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\SnappyExtension;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class SnappyExtensionTest extends TestCase
{
    private SnappyExtension $extension;

    private ContainerBuilder $container;

    public function setUp(): void
    {
        $this->extension = new SnappyExtension();
        $this->container = new ContainerBuilder();

        $this->container->setDefinition(
            StreamFactoryInterface::class,
            new Definition(Psr17Factory::class),
        );
    }

    public function testLoadEmptyConfiguration(): void
    {
        $configuration = [];

        $this->extension->load(
            $configuration,
            $this->container,
        );

        $this->assertEquals(
            \array_keys($this->container->getDefinitions()),
            [
                'service_container',
                StreamFactoryInterface::class,
            ],
        );
    }

    public function testDompdfBackendConfiguration(): void
    {
        $configuration = [
            'snappy' => [
                'backends' => [
                    'myBackend' => [
                        'dompdf' => [
                            'options' => [
                                'pageOrientation' => PageOrientation::LANDSCAPE->value,
                                'extraOptions' => [
                                    'construct' => [
                                        'tempDir' => '/tmp',
                                    ],
                                    'output' => [
                                        'compress' => '1',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->extension->load($configuration, $this->container);

        $this->assertEquals(
            \array_keys($this->container->getDefinitions()),
            [
                'service_container',
                StreamFactoryInterface::class,
                'snappy.backend.myBackend.factory',
                'snappy.backend.myBackend',
            ]
        );

        $streamFactory = $this->container->get(StreamFactoryInterface::class);

        $this->assertInstanceOf(StreamFactoryInterface::class, $streamFactory);

        $factory = $this->container->get('snappy.backend.myBackend.factory');

        $this->assertInstanceOf(DompdfFactory::class, $factory);
        $this->assertEquals(
            $factory,
            new DompdfFactory($streamFactory)
        );

        $backend = $this->container->get('snappy.backend.myBackend');

        $this->assertInstanceOf(DompdfAdapter::class, $backend);
        $this->assertEquals(
            $factory,
            new DompdfFactory($streamFactory),
        );

        $this->assertEquals(
            $backend,
            new DompdfAdapter(
                $factory,
                new Options(
                    PageOrientation::LANDSCAPE,
                    [
                        'construct' => ['tempDir' => '/tmp'],
                        'output' => ['compress' => '1'],
                    ],
                ),
                $streamFactory,
            ),
        );
    }
}
