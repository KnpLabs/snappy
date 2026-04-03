<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony\Tests\DependencyInjection;

use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessAdapter;
use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessFactory;
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
use Psr\Http\Message\UriFactoryInterface;
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

        $this->container->setDefinition(
            UriFactoryInterface::class,
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
                UriFactoryInterface::class,
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
                UriFactoryInterface::class,
                'knplabs.snappy.core.backend.factory.myBackend',
                'knplabs.snappy.core.backend.adapter.myBackend',
                'knplabs.snappy.core.frontend.domdocumenttopdf.myBackend',
                'knplabs.snappy.core.frontend.htmlfiletopdf.myBackend',
                'knplabs.snappy.core.frontend.htmltopdf.myBackend',
                'knplabs.snappy.core.frontend.streamtopdf.myBackend',
                'knplabs.snappy.core.frontend.uritopdf.myBackend',
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
            new IsEqual(
                new Frontend\HtmlFileToPdf(
                    $backend,
                    $streamFactory,
                )
            ),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.htmltopdf.myBackend'),
            new IsEqual(
                new Frontend\HtmlToPdf(
                    $backend,
                    $streamFactory,
                )
            ),
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

    public function testChromeHeadlessBackendConfiguration(): void
    {
        $configuration = [
            'snappy' => [
                'backends' => [
                    'chromeBackend' => [
                        'chrome_headless' => [
                            'binary' => 'google-chrome',
                            'timeout' => 60,
                            'options' => [
                                'pageOrientation' => 'portrait',
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
                UriFactoryInterface::class,
                'knplabs.snappy.core.backend.factory.chromeBackend',
                'knplabs.snappy.core.backend.adapter.chromeBackend',
                'knplabs.snappy.core.frontend.domdocumenttopdf.chromeBackend',
                'knplabs.snappy.core.frontend.htmlfiletopdf.chromeBackend',
                'knplabs.snappy.core.frontend.htmltopdf.chromeBackend',
                'knplabs.snappy.core.frontend.streamtopdf.chromeBackend',
                'knplabs.snappy.core.frontend.uritopdf.chromeBackend',
            ]
        );

        $streamFactory = $this->container->get(StreamFactoryInterface::class);
        $uriFactory = $this->container->get(UriFactoryInterface::class);

        self::assertInstanceOf(StreamFactoryInterface::class, $streamFactory);
        self::assertInstanceOf(UriFactoryInterface::class, $uriFactory);

        $factory = $this->container->get('knplabs.snappy.core.backend.factory.chromeBackend');

        self::assertInstanceOf(ChromeHeadlessFactory::class, $factory);
        self::assertThat(
            $factory,
            new IsEqual(new ChromeHeadlessFactory('google-chrome', 60, $streamFactory, $uriFactory))
        );

        $backend = $this->container->get('knplabs.snappy.core.backend.adapter.chromeBackend');

        self::assertInstanceOf(ChromeHeadlessAdapter::class, $backend);
        self::assertThat(
            $backend,
            new IsEqual(
                new ChromeHeadlessAdapter(
                    'google-chrome',
                    60,
                    $factory,
                    new Options(PageOrientation::Portrait, []),
                    $streamFactory,
                    $uriFactory,
                ),
            ),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.uritopdf.chromeBackend'),
            new IsEqual(
                new Frontend\UriToPdf(
                    $backend,
                    $streamFactory,
                )
            ),
        );

        self::assertThat(
            $this->container->get('knplabs.snappy.core.frontend.htmlfiletopdf.chromeBackend'),
            new IsEqual(
                new Frontend\HtmlFileToPdf(
                    $backend,
                    $streamFactory,
                )
            ),
        );
    }
}
