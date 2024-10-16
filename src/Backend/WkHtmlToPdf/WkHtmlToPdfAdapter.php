<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use KNPLabs\Snappy\Core\Stream\FileStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
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
        self::validateOptions($options);

        $this->factory = $factory;
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
        $outputFile = SplResourceInfo::fromTmpFile();

        $process = new Process(
            command: [
                $this->binary,
                '--log-level', 'none',
                '--quiet',
                ...$this->compileOptions(),
                $uri->toString(),
                $outputFile->getPathname(),
            ],
            timeout: $this->timeout,
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->streamFactory->createFromResource($outputFile->resource);
    }

    private static function validateOptions(Options $options): void
    {
        $optionTypes = [];

        foreach ($options->extraOptions as $option) {
            if (!$option instanceof ExtraOption) {
                throw new \InvalidArgumentException(sprintf(
                    "Invalid option type provided. Expected \"%s\", received \"%s\".",
                    ExtraOption::class,
                    gettype($option) === 'object' ? get_class($option) : gettype($option),
                ));
            }

            if (\in_array($option::class, $optionTypes, true) && !$option->isRepeatable()) {
                throw new \InvalidArgumentException(sprintf(
                    "Duplicate option type provided: \"%s\".",
                    $option::class,
                ));
            }

            $optionTypes[] = $option::class;
        }
    }

    /**
     * @return array<string|int|float>
     */
    private function compileOptions(): array
    {
        return array_reduce(
            $this->options->extraOptions,
            fn (array $carry, ExtraOption $extraOption) =>
                $extraOption instanceof ExtraOption\OrientationOption && $this->options->pageOrientation !== null
                    ? [
                        ...$carry,
                        ...ExtraOption\OrientationOption::fromPageOrientation($this->options->pageOrientation)->compile(),
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
