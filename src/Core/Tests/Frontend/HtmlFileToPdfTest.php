<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use KNPLabs\Snappy\Core\Frontend\HtmlFileToPdf;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Constraint;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class HtmlFileToPdfTest extends TestCase
{
    private StreamInterface $output;

    private StreamFactoryInterface $streamFactory;

    private \SplFileInfo $file;

    public function setUp(): void
    {
        $this->output = $this->createStub(StreamInterface::class);
        $this->streamFactory = new Psr17Factory();
        $this->file = SplResourceInfo::fromTmpFile();

        fwrite($this->file->resource, '<html />');
    }

    public function testWithDOMDocumentToPdf(): void
    {
        $backend = $this->createMock(Adapter\DOMDocumentToPdf::class);
        $frontend = new HtmlFileToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromDOMDocument')
            ->with(
                new Constraint\Callback(
                    function (\DOMDocument $document) {
                        $expected = new \DOMDocument();
                        $expected->loadHTML('<html />');

                        return $document->saveHTML() === $expected->saveHTML();
                    }
                )
            )
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromHtmlFile($this->file),
            $this->output,
        );
    }

    public function testWithHtmlToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlToPdf::class);
        $frontend = new HtmlFileToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtml')
            ->with('<html />')
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromHtmlFile($this->file),
            $this->output,
        );
    }

    public function testWithHtmlFileToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlFileToPdf::class);
        $frontend = new HtmlFileToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtmlFile')
            ->with($this->file)
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromHtmlFile($this->file),
            $this->output,
        );
    }

    public function testWithStreamToPdf(): void
    {
        $backend = $this->createMock(Adapter\StreamToPdf::class);
        $frontend = new HtmlFileToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromStream')
            ->with(
                new Constraint\Callback(
                    fn (StreamInterface $stream) => '<html />' === (string) $stream,
                )
            )
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromHtmlFile($this->file),
            $this->output,
        );
    }
}
