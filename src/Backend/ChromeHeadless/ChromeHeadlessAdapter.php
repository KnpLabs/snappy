<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless;

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

final class ChromeHeadlessAdapter implements HtmlFileToPdf, UriToPdf
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
        ChromeHeadlessFactory $factory,
        Options $options,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UriFactoryInterface $uriFactory,
    ) {
        $this->factory = $factory;
        $this->options = $options;

        $this->compileOptions();
    }

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        $filepath = $file->getRealPath();

        if (false === $filepath) {
            throw new \RuntimeException(\sprintf('File not found: %s.', $file->getPathname()));
        }

        return $this->generateFromUri(
            $this->uriFactory->createUri('file://' . $filepath)
        );
    }

    public function generateFromUri(UriInterface $uri): StreamInterface
    {
        $outputFile = SplResourceInfo::fromTmpFile();

        $process = new Process(
            command: [
                $this->binary,
                '--headless=new',
                '--disable-gpu',
                '--print-to-pdf=' . $outputFile->getPathname(),
                ...$this->compileOptions(),
                (string) $uri,
            ],
            timeout: $this->timeout,
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->streamFactory->createStreamFromResource($outputFile->resource);
    }

    /**
     * @return array<string>
     */
    private function compileOptions(): array
    {
        $options = [];

        if ($this->options->pageOrientation instanceof PageOrientation) {
        }

        $optionTypes = [];

        foreach ($this->options->extraOptions as $extraOption) {
            if (!$extraOption instanceof ExtraOption) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Invalid option type provided. Expected "%s", received "%s".',
                        ExtraOption::class,
                        get_debug_type($extraOption),
                    )
                );
            }

            if ($extraOption->repeatable && \in_array($extraOption::class, $optionTypes, true)) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Duplicate option type provided: "%s".',
                        $extraOption::class,
                    )
                );
            }

            $options = [
                ...$options,
                ...$extraOption->command,
            ];

            $optionTypes[] = $extraOption::class;
        }

        return $options;
    }
}
