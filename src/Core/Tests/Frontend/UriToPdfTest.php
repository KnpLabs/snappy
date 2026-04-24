<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Frontend\UriToPdf;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Constraint;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
#[CoversNothing]
final class UriToPdfTest extends TestCase
{
    private StreamInterface $output;

    private StreamFactoryInterface $streamFactory;

    protected function setUp(): void
    {
        $this->output = self::createStub(StreamInterface::class);
        $this->streamFactory = new Psr17Factory();
    }

    public function testWithUriToPdf(): void
    {
        $backend = $this->createMock(Adapter\UriToPdf::class);
        $frontend = new UriToPdf($backend, $this->streamFactory);

        $uri = new Uri('https://example.com');

        $backend
            ->method('generateFromUri')
            ->with($uri)
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromUri($uri),
            $this->output,
        );
    }

    public function testWithHtmlFileToPdf(): void
    {
        $backend = $this->createMock(Adapter\HtmlFileToPdf::class);
        $frontend = new UriToPdf($backend, $this->streamFactory);

        $uri = new Uri('data:text/html,<html />');

        $backend
            ->method('generateFromHtmlFile')
            ->with(
                new Constraint\Callback(
                    static fn (\SplFileInfo $file): bool => '<html />' === file_get_contents($file->getPathname())
                )
            )
            ->willReturn($this->output)
        ;

        self::assertSame(
            $frontend->generateFromUri($uri),
            $this->output,
        );
    }

    public function testWithOptions(): void
    {
        $backend = $this->createMock(Adapter\UriToPdf::class);
        $frontend = new UriToPdf($backend, $this->streamFactory);

        $options = Options::create();

        $backend
            ->method('withOptions')
            ->with($options)
            ->willReturnSelf()
        ;

        $reconfigured = $frontend->withOptions($options);

        self::assertNotSame($frontend, $reconfigured);
    }
}

