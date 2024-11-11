<?php

declare(strict_types=1);

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

/**
 * @internal
 *
 * @coversNothing
 */
final class SnappyExtensionTest extends TestCase
{
    private SnappyExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
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

        self::assertSame(
            array_keys($this->container->getDefinitions()),
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

        self::assertSame(
            array_keys($this->container->getDefinitions()),
            [
                'service_container',
                StreamFactoryInterface::class,
                'snappy.backend.myBackend.factory',
                'snappy.backend.myBackend',
            ]
        );

        $streamFactory = $this->container->get(StreamFactoryInterface::class);

        self::assertInstanceOf(StreamFactoryInterface::class, $streamFactory);

        $factory = $this->container->get('snappy.backend.myBackend.factory');

        self::assertInstanceOf(DompdfFactory::class, $factory);
        self::assertEquals(
            $factory,
            new DompdfFactory($streamFactory)
        );

        $backend = $this->container->get('snappy.backend.myBackend');

        self::assertInstanceOf(DompdfAdapter::class, $backend);
        self::assertEquals(
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
