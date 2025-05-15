<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\Dompdf\Tests;

use KNPLabs\Snappy\Backend\Dompdf\DompdfFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
#[CoversNothing]
final class DomAdapterTest extends TestCase
{
    private DompdfFactory $factory;
    private Options $options;

    protected function setUp(): void
    {
        $this->factory = new DompdfFactory(new Psr17Factory());
        $this->options = Options::create()
            ->withExtraOptions([
                'construct' => [
                    'chroot' => __DIR__,
                ],
            ])
        ;
    }

    public function testGenerateFromDOMDocument(): void
    {
        $document = new \DOMDocument();
        $document->loadHTMLFile(__DIR__.'/files/order.html');

        $domPdfAdapter = $this->factory->create($this->options);
        $stream = $domPdfAdapter->generateFromDOMDocument($document);

        self::assertPdfStreamEqualsFile(
            $stream,
            __DIR__.'/files/order.pdf',
        );
    }

    public function testGenerateFromHtmlFile(): void
    {
        $file = new \SplFileInfo(__DIR__.'/files/order.html');

        $domPdfAdapter = $this->factory->create($this->options);
        $stream = $domPdfAdapter->generateFromHtmlFile($file);

        self::assertPdfStreamEqualsFile(
            $stream,
            __DIR__.'/files/order.pdf',
        );
    }

    public function testGenerateFromHtml(): void
    {
        $html = file_get_contents(__DIR__.'/files/order.html');

        self::assertNotFalse($html);

        $domPdfAdapter = $this->factory->create($this->options);
        $stream = $domPdfAdapter->generateFromHtml($html);

        self::assertPdfStreamEqualsFile(
            $stream,
            __DIR__.'/files/order.pdf',
        );
    }

    private static function assertPdfStreamEqualsFile(StreamInterface $stream, string $file): void
    {
        $to = tmpfile();
        $from = $stream->detach();

        self::assertNotNull($from);

        stream_copy_to_stream($from, $to);

        $path = stream_get_meta_data($to)['uri'];

        $controlDocument = new \Imagick();
        $compareDocument = new \Imagick();

        $controlDocument->readImage($file);
        $compareDocument->readImage($path);

        [$diffDocument, $diffPixels] = $controlDocument
            ->compareImages($compareDocument, \Imagick::METRIC_ABSOLUTEERRORMETRIC)
        ;

        if (0.0 !== $diffPixels) {
            file_put_contents("{$file}.diff.pdf", $diffDocument);
        }

        self::assertSame($diffPixels, 0.0);
    }
}
