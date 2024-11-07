<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\Tests;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption\DisableGpu;
use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption\Headless;
use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption\PrintToPdf;
use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumAdapter;
use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use SplFileInfo;
use RuntimeException;

final class HeadlessChromiumAdapterTest extends TestCase
{
    private HeadlessChromiumFactory $factory;

    private Options $options;

    private StreamFactoryInterface $streamFactory;

    private HeadlessChromiumAdapter $adapter;

    private string $directory;

    private SplFileInfo $outputFile;

    protected function setUp(): void
    {
        $this->directory = __DIR__;
        $this->outputFile = new SplFileInfo($this->directory . '/file.pdf');
        $this->options = new Options(null, [new Headless(), new PrintToPdf($this->outputFile->getPathname()), new DisableGpu()]);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->factory = new HeadlessChromiumFactory(
            'chromium',
            120,
            $this->streamFactory,
        );
        $this->adapter = $this->factory->create($this->options);
    }

    public function testGenerateFromUri(): void
    {
        $url = $this->createMock(UriInterface::class);
        $url->method('__toString')->willReturn('https://google.com');

        $resultStream = $this->adapter->generateFromUri($url);

        $this->assertNotNull($resultStream);
        $this->assertInstanceOf(StreamInterface::class, $resultStream);

        \unlink($this->directory . '/file.pdf');
    }

    public function testGetPrintToPdfFilePath(): void
    {
        $filePath = $this->adapter->getPrintToPdfFilePath();
        $this->assertEquals($this->outputFile->getPathname(), $filePath);

        $optionsWithoutPrintToPdf = new Options(null, [new Headless(), new DisableGpu()]);
        $adapterWithoutPrintToPdf = $this->factory->create($optionsWithoutPrintToPdf);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing option print to pdf.');

        $adapterWithoutPrintToPdf->getPrintToPdfFilePath();
    }
}
