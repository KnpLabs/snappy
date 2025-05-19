<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\Tests\DependencyInjection;

use KNPLabs\Snappy\Backend\Dompdf\DompdfAdapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Core\Frontend;
use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\SnappyExtension;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
#[CoversNothing]
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
                                'pageOrientation' => 'landscape',
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
                'knplabs.snappy.core.backend.factory.myBackend',
                'knplabs.snappy.core.backend.adapter.myBackend',
                'knplabs.snappy.core.frontend.domdocumenttopdf.myBackend',
                'knplabs.snappy.core.frontend.htmlfiletopdf.myBackend',
                'knplabs.snappy.core.frontend.htmltopdf.myBackend',
                'knplabs.snappy.core.frontend.streamtopdf.myBackend',
            ]
        );

        $streamFactory = $this->container->get(StreamFactoryInterface::class);

        self::assertInstanceOf(StreamFactoryInterface::class, $streamFactory);

        $factory = $this->container->get('knplabs.snappy.core.backend.factory.myBackend');

        self::assertInstanceOf(DompdfFactory::class, $factory);
        self::assertThat(
            $factory,
            new IsEqual(new DompdfFactory($streamFactory))
        );

        $backend = $this->container->get('knplabs.snappy.core.backend.adapter.myBackend');

        self::assertInstanceOf(DompdfAdapter::class, $backend);
        self::assertThat(
            $factory,
            new IsEqual(new DompdfFactory($streamFactory))
        );

        self::assertThat(
            $backend,
            new IsEqual(
                new DompdfAdapter(
                    $factory,
                    new Options(
                        PageOrientation::Landscape,
                        [
                            'construct' => ['tempDir' => '/tmp'],
                            'output' => ['compress' => '1'],
                        ],
                    ),
                    $streamFactory,
                ),
            ),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.domdocumenttopdf.myBackend'),
            new IsEqual(
                new Frontend\DOMDocumentToPdf(
                    $backend,
                    $streamFactory,
                ),
            )
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.htmlfiletopdf.myBackend'),
            new IsEqual(new Frontend\HtmlFileToPdf(
                $backend,
                $streamFactory,
            )),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.htmltopdf.myBackend'),
            new IsEqual(new Frontend\HtmlToPdf(
                $backend,
                $streamFactory,
            )),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.streamtopdf.myBackend'),
            new IsEqual(
                new Frontend\StreamToPdf(
                    $backend,
                    $streamFactory,
                )
            ),
        );
    }
}
