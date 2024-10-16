<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Frontend\StreamToPdf;
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
final class StreamToPdfTest extends TestCase
{
    private StreamInterface $output;

    private StreamFactoryInterface $streamFactory;

    private StreamInterface $stream;

    public function setUp(): void
    {
        $this->output = $this->createStub(StreamInterface::class);
        $this->streamFactory = new Psr17Factory();
        $this->stream = $this->streamFactory->createStream('<html />');
    }

    public function testWithDOMDocumentToPdf(): void
    {
        $backend = $this->createMock(Adapter\DOMDocumentToPdf::class);
        $frontend = new StreamToPdf($backend, $this->streamFactory);

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
            $frontend->generateFromStream($this->stream),
            $this->output,
        );
    }

    public function testWithHtmlToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlToPdf::class);
        $frontend = new StreamToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtml')
            ->with((string) $this->stream)
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromStream($this->stream),
            $this->output,
        );
    }

    public function testWithHtmlFileToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlFileToPdf::class);
        $frontend = new StreamToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtmlFile')
            ->with(
                new Constraint\Callback(
                    fn (\SplFileInfo $file) => '<html />' === file_get_contents($file->getPathname())
                )
            )
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromStream($this->stream),
            $this->output,
        );
    }

    public function testWithStreamToPdf(): void
    {
        $backend = $this->createMock(Adapter\StreamToPdf::class);
        $frontend = new StreamToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromStream')
            ->with($this->stream)
            ->willReturn($this->output)
        ;

        $this->assertSame(
            $frontend->generateFromStream($this->stream),
            $this->output,
        );
    }
}
