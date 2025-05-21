<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Tests;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption\Title;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\WkHtmlToPdfAdapter;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\WkHtmlToPdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 */
#[CoversNothing]
final class WkHtmlToPdfAdapterTest extends TestCase
{
    private WkHtmlToPdfFactory $factory;

    private WkHtmlToPdfAdapter $wkHtmlToPdfAdapter;

    /**
     * @var MockObject&StreamFactoryInterface
     */
    private MockObject $streamFactory;

    /**
     * @var MockObject&UriFactoryInterface
     */
    private MockObject $uriFactory;

    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir();
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->uriFactory = $this->createMock(UriFactoryInterface::class);

        $this->factory = new WkHtmlToPdfFactory(
            'wkhtmltopdf',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $this->wkHtmlToPdfAdapter = $this->factory->create(new Options(null, []));
    }

    public function testGenerateFromHtmlFile(): void
    {
        $htmlContent = '<html><body><h1>Test PDF</h1></body></html>';
        $testFilePath = $this->tempDir.'/test.html';
        file_put_contents($testFilePath, $htmlContent);

        $this->uriFactory
            ->method('createUri')
            ->with($testFilePath)
            ->willReturn(new Uri($testFilePath))
        ;

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('%PDF-1.4 content');

        $this->streamFactory
            ->expects(self::once())
            ->method('createStreamFromResource')
            ->willReturn($stream)
        ;

        try {
            $resultStream = $this->wkHtmlToPdfAdapter->generateFromHtmlFile(new \SplFileInfo($testFilePath));
        } catch (\Exception $exception) {
            self::fail('Erreur lors de la génération du PDF : '.$exception->getMessage());
        }

        self::assertNotEmpty($resultStream->getContents());

        unlink($testFilePath);
    }

    public function testGenerateFromInvalidHtmlFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found:');

        $this->wkHtmlToPdfAdapter->generateFromHtmlFile(new \SplFileInfo($this->tempDir.'/nonexistent.html'));
    }

    public function testGenerateWithAdditionalOptions(): void
    {
        $htmlContent = '<html><head><title>Test PDF</title></head><body><h1>Test PDF</h1></body></html>';
        $testFilePath = $this->tempDir.'/test_with_options.html';
        file_put_contents($testFilePath, $htmlContent);

        $options = new Options(
            null,
            [
                new Title('Test PDF Title'),
            ]
        );

        $this->factory = new WkHtmlToPdfFactory(
            'wkhtmltopdf',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $this->wkHtmlToPdfAdapter = $this->factory->create($options);

        $realpath = realpath($testFilePath);

        self::assertIsString($realpath);

        $this->uriFactory
            ->method('createUri')
            ->with($testFilePath)
            ->willReturn(new Uri($realpath))
        ;

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('%PDF-1.4 content');

        $this->streamFactory
            ->method('createStreamFromResource')
            ->willReturn($stream)
        ;

        $resultStream = $this->wkHtmlToPdfAdapter->generateFromHtmlFile(new \SplFileInfo($testFilePath));

        self::assertNotEmpty($resultStream->getContents());

        unlink($testFilePath);
    }
}
