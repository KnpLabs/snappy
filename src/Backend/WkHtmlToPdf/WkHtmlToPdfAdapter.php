<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Stream\FileStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Process;
use SplFileInfo;
use Exception;

final class WkHtmlToPdfAdapter implements HtmlFileToPdf, UriToPdf
{
    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    /**
     * @param non-empty-string $binary
     * @param positive-int     $timeout
     */
    public function __construct(
        private string $binary,
        private int $timeout,
        WkHtmlToPdfFactory $factory,
        Options $options,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UriFactoryInterface $uriFactory,
    ) {
        $this->factory = $factory;

        foreach ($options->extraOptions as $extraOption) {
            if (!$extraOption instanceof ExtraOption) {
                throw new \InvalidArgumentException("Invalid option type");
            }
        }

        $this->options = $options;
    }

    public function generateFromHtmlFile(SplFileInfo $file): StreamInterface
    {
        $filepath = $file->getRealPath();

        if ($filepath === false) {
            throw new \RuntimeException("File not found: {$file->getPathname()}.");
        }

        return $this->generateFromUri(
            $this->uriFactory->createUri($filepath)->withScheme('file')
        );
    }

    public function generateFromUri(UriInterface $uri): StreamInterface
    {
        $outputStream = FileStream::createTmpFile($this->streamFactory);

        $process = new Process(
            command: [
                $this->binary,
                ...$this->compileOptions(),
                $uri->toString(),
                $outputStream->file->getPathname(),
            ],
            timeout: $this->timeout,
        );

        return $outputStream;
    }

    /**
     * @return array<string|int|float>
     */
    private function compileOptions(): array
    {
        return array_reduce(
            $this->options->extraOptions,
            fn (array $carry, ExtraOption $extraOption) =>
                $this->options->pageOrientation !== null && $extraOption instanceof ExtraOption\Orientation
                    ? [
                        ...$carry,
                        ...(new ExtraOption\Orientation($this->options->pageOrientation->value))->compile(),
                    ]
                    : [
                        ...$carry,
                        ...$extraOption->compile(),
                    ]
            ,
            [],
        );
    }
}
