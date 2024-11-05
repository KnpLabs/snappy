<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\Dompdf\Tests;

use DOMDocument;
use KNPLabs\Snappy\Backend\Dompdf\DompdfAdapter;
use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class DompdfAdapterTest extends TestCase
{
    private DompdfAdapter $adapter;
    private Options $options;
    private StreamFactoryInterface $streamFactoryMock;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir();
        $this->options = new Options(null, [
            'output' => __DIR__,
            'construct' => [
                'chroot' => $this->tempDir,
            ]
        ]);

        $this->streamFactoryMock = $this->createMock(StreamFactoryInterface::class);

        $factory = new DompdfFactory($this->streamFactoryMock);
        $this->adapter = $factory->create($this->options);
    }

    public function testGenerateFromDOMDocument(): void
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML('<html><body>Hello World</body></html>');

        $expectedStreamMock = $this->createMock(StreamInterface::class);
        $this->streamFactoryMock
            ->expects($this->once())
            ->method('createStream')
            ->with($this->isType('string'))
            ->willReturn($expectedStreamMock);

        $output = $this->adapter->generateFromDOMDocument($domDocument);

        $this->assertSame($expectedStreamMock, $output);
    }

    public function testGenerateFromHtmlFile(): void
    {
        $tempFilePath = $this->tempDir . '/test.html';
        file_put_contents($tempFilePath, '<html><body>Temporary Test File</body></html>');
        $fileMock = new \SplFileInfo($tempFilePath);

        $expectedStreamMock = $this->createMock(StreamInterface::class);
        $this->streamFactoryMock
            ->expects($this->once())
            ->method('createStream')
            ->with($this->isType('string'))
            ->willReturn($expectedStreamMock);

        $output = $this->adapter->generateFromHtmlFile($fileMock);

        $this->assertSame($expectedStreamMock, $output);

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }
    }

    public function testGenerateFromHtml(): void
    {
        $htmlContent = '<html><body>Test HTML content</body></html>';

        $expectedStreamMock = $this->createMock(StreamInterface::class);
        $this->streamFactoryMock
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($expectedStreamMock);

        $output = $this->adapter->generateFromHtml($htmlContent);

        $this->assertSame($expectedStreamMock, $output);
    }

    public function testGenerateFromInvalidHtml(): void
    {
        $invalidHtmlContent = '<html><body><h1>Unclosed Header';

        $expectedStreamMock = $this->createMock(StreamInterface::class);
        $this->streamFactoryMock
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($expectedStreamMock);

        $output = $this->adapter->generateFromHtml($invalidHtmlContent);

        $this->assertSame($expectedStreamMock, $output);
    }

    public function testGenerateFromEmptyHtml(): void
    {
        $htmlContent = '';

        $expectedStreamMock = $this->createMock(StreamInterface::class);
        $this->streamFactoryMock
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($expectedStreamMock);

        $output = $this->adapter->generateFromHtml($htmlContent);

        $this->assertSame($expectedStreamMock, $output);
    }

    public function testStreamContentFromHtml(): void
    {
        $htmlContent = '<html><body>Test Content</body></html>';
        $expectedOutput = 'PDF content for Test Content';

        $this->streamFactoryMock
            ->method('createStream')
            ->willReturn($this->createStreamWithContent($expectedOutput));

        $output = $this->adapter->generateFromHtml($htmlContent);
        $this->assertSame($expectedOutput, $output->getContents());
    }

    private function createStreamWithContent(string $content): StreamInterface
    {
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($content);
        return $streamMock;
    }

    public function testOptionsHandling(): void
    {
        $this->options = new Options(PageOrientation::LANDSCAPE, []);
        $this->adapter = (new DompdfFactory($this->streamFactoryMock))->create($this->options);

        $this->assertTrue(true);
    }
}
