<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Core\Stream\FileStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
        foreach ($options->extraOptions as $extraOption) {
            if (!$extraOption instanceof ExtraOption) {
                throw new \InvalidArgumentException('Invalid option type.');
            }
        }

        $this->factory = $factory;
        $this->options = $options;
    }

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        $filepath = $file->getRealPath();

        if (false === $filepath) {
            throw new \RuntimeException(\sprintf('File not found: %s.', $file->getPathname()));
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
                '--quiet',
                ...$this->compileOptions(),
                $uri->toString(),
                $outputStream->file->getPathname(),
            ],
            timeout: $this->timeout,
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $outputStream;
    }

    /**
     * @return array<float|int|string>
     */
    private function compileOptions(): array
    {
        return array_reduce(
            $this->options->extraOptions,
            fn (array $carry, ExtraOption $extraOption): array => $extraOption instanceof ExtraOption\OrientationOption && $this->options->pageOrientation instanceof PageOrientation
                    ? [
                        ...$carry,
                        ...ExtraOption\OrientationOption::fromPageOrientation($this->options->pageOrientation)->compile(),
                    ]
                    : [
                        ...$carry,
                        ...$extraOption->compile(),
                    ],
            [],
        );
    }
}
