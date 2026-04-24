<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\Tests;

use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessAdapter;
use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessFactory;
use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 */
#[CoversClass(ChromeHeadlessAdapter::class)]
final class ChromeHeadlessAdapterTest extends TestCase
{
    private ChromeHeadlessFactory $factory;

    private ChromeHeadlessAdapter $adapter;

    /**
     * @var MockObject&StreamFactoryInterface
     */
    private MockObject $streamFactory;

    /**
     * @var MockObject&UriFactoryInterface
     */
    private MockObject $uriFactory;

    protected function setUp(): void
    {
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->uriFactory = $this->createMock(UriFactoryInterface::class);

        $this->factory = new ChromeHeadlessFactory(
            'google-chrome',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $this->adapter = $this->factory->create(new Options(null, []));
    }

    #[DoesNotPerformAssertions]
    public function testFactoryCreatesAdapter(): void
    {
        $options = new Options(
            PageOrientation::Landscape,
            [
                new ExtraOption\NoSandbox(),
                new ExtraOption\WindowSize('1920,1080'),
            ]
        );

        $this->factory->create($options);

    }

    public function testWithOptionsCreatesNewAdapter(): void
    {
        $newOptions = new Options(
            PageOrientation::Landscape,
            [new ExtraOption\NoSandbox()]
        );

        $newAdapter = $this->adapter->withOptions($newOptions);

        self::assertNotSame($this->adapter, $newAdapter);
    }

    public function testGenerateFromInvalidHtmlFileThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found:');

        $this->adapter->generateFromHtmlFile(new \SplFileInfo('/nonexistent/file.html'));
    }

#[DoesNotPerformAssertions]
    public function testFactoryCreatesAdapterWithAllExtraOptionTypes(): void
    {
        $options = new Options(
            null,
            [
                new ExtraOption\NoSandbox(),
                new ExtraOption\DisableDevShmUsage(),
                new ExtraOption\NoPdfHeaderFooter(),
                new ExtraOption\VirtualTimeBudget(5000),
                new ExtraOption\WindowSize('1920,1080'),
                new ExtraOption\Timeout(30000),
                new ExtraOption\RunAllCompositorStagesBeforeDraw(),
            ]
        );

        $this->factory->create($options);

    }

#[DoesNotPerformAssertions]
    public function testFactoryCreatesAdapterWithPageOrientation(): void
    {
        $options = new Options(
            PageOrientation::Portrait,
            []
        );

        $this->factory->create($options);

    }

#[DoesNotPerformAssertions]
    public function testFactoryCreatesAdapterWithCustomBinaryPath(): void
    {
        $factory = new ChromeHeadlessFactory(
            '/usr/bin/chromium',
            120,
            $this->streamFactory,
            $this->uriFactory
        );

        $factory->create(new Options(null, []));

    }

#[DoesNotPerformAssertions]
    public function testFactoryCreatesAdapterWithMinimalTimeout(): void
    {
        $factory = new ChromeHeadlessFactory(
            'google-chrome',
            1,
            $this->streamFactory,
            $this->uriFactory
        );

        $factory->create(new Options(null, []));

    }
}
