<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Frontend\DOMDocumentToPdf;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Constraint;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
#[CoversNothing]
final class DOMDocumentToPdfTest extends TestCase
{
    private StreamInterface $output;

    private StreamFactoryInterface $streamFactory;

    private \DOMDocument $document;

    protected function setUp(): void
    {
        $this->output = self::createStub(StreamInterface::class);
        $this->streamFactory = new Psr17Factory();
        $this->document = new \DOMDocument();
        $this->document->loadHTML('<html />');
    }

    public function testWithDOMDocumentToPdf(): void
    {
        $backend = $this->createMock(Adapter\DOMDocumentToPdf::class);
        $frontend = new DOMDocumentToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromDOMDocument')
            ->with($this->document)
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromDOMDocument($this->document),
            $this->output,
        );
    }

    public function testWithHtmlToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlToPdf::class);
        $frontend = new DOMDocumentToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtml')
            ->with($this->document->saveHTML())
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromDOMDocument($this->document),
            $this->output,
        );
    }

    public function testWithHtmlFileToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlFileToPdf::class);
        $frontend = new DOMDocumentToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromHtmlFile')
            ->with(
                new Constraint\Callback(
                    fn (\SplFileInfo $file): bool => $this->document->saveHTML() === file_get_contents($file->getPathname())
                )
            )
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromDOMDocument($this->document),
            $this->output,
        );
    }

    public function testWithStreamToPdf(): void
    {
        $backend = $this->createMock(Adapter\StreamToPdf::class);
        $frontend = new DOMDocumentToPdf($backend, $this->streamFactory);

        $backend
            ->method('generateFromStream')
            ->with(
                new Constraint\Callback(
                    fn (StreamInterface $stream): bool => $this->document->saveHTML() === (string) $stream
                )
            )
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromDOMDocument($this->document),
            $this->output,
        );
    }
}
