<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
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
        $this->validateOptions($options);

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
            $this->uriFactory->createUri($filepath)
        );
    }

    public function generateFromUri(UriInterface $uri): StreamInterface
    {
        $outputFile = SplResourceInfo::fromTmpFile();

        $process = new Process(
            command: [
                $this->binary,
                '--log-level',
                'none',
                '--quiet',
                ...$this->compileOptions(),
                (string) $uri,
                $outputFile->getPathname(),
            ],
            timeout: $this->timeout,
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->streamFactory->createStreamFromResource($outputFile->resource);
    }

    private function validateOptions(Options $options): void
    {
        $optionTypes = [];

        foreach ($options->extraOptions as $option) {
            if (!$option instanceof ExtraOption) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Invalid option type provided. Expected "%s", received "%s".',
                        ExtraOption::class,
                        get_debug_type($option),
                    )
                );
            }

            if (\in_array($option::class, $optionTypes, true) && !$option->isRepeatable()) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Duplicate option type provided: "%s".',
                        $option::class,
                    )
                );
            }

            $optionTypes[] = $option::class;
        }
    }

    /**
     * @return array<float|int|string>
     */
    private function compileOptions(): array
    {
        return array_reduce(
            $this->options->extraOptions,
            fn (array $carry, ExtraOption $extraOption): array => $extraOption instanceof ExtraOption\Orientation && $this->options->pageOrientation instanceof PageOrientation
                ? [
                    ...$carry,
                    ...(new ExtraOption\Orientation($this->options->pageOrientation))->getCommand(),
                ]
                : [
                    ...$carry,
                    ...$extraOption->getCommand(),
                ],
            [],
        );
    }
}
