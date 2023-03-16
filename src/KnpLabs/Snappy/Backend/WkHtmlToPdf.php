<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend;

use KnpLabs\Snappy\FileStream;
use KnpLabs\Snappy\OptionCollection;
use KnpLabs\Snappy\OptionsValidator;
use KnpLabs\Snappy\Process\ProcessRunner;
use LogicException;
use SplFileInfo;

class WkHtmlToPdf implements FileToPdfBackend, UriToPdfBackend
{
    public function __construct(
        private readonly string $binary,
        private readonly ProcessRunner $processRunner,
        private readonly ?OptionsValidator $optionsValidator = null,
    ) {
    }

    public function validateOptions(array $options): void
    {
        if (null === $this->optionsValidator) {
            return;
        }

        $this->optionsValidator->validateOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromFile(SplFileInfo $file, iterable $options = []): FileStream
    {
        return $this->generateFromUri($file->getRealPath(), $options);
    }

    public function generateFromUri(string $uri, iterable $options = []): FileStream
    {
        $outputPath = sprintf('%s/%s.pdf',
            sys_get_temp_dir(),
            uniqid('snappy')
        );

        $options = OptionCollection::fromFlatIterable($options);

        $command = $this->getCommand($uri, $outputPath, $options);

        $processOuput = $this->processRunner->executeCommand($command);
        $processOuput->ensureSuccessful();

        return new FileStream(new SplFileInfo($outputPath));
    }

    private function getCommand(string $input, string $output, OptionCollection $options): string
    {
        if (null === $this->binary) {
            throw new LogicException('You must define a binary prior to conversion.');
        }

        $escapedBinary = \escapeshellarg($this->binary);
        $binary = is_executable($escapedBinary) ? $escapedBinary : $this->binary;

        return sprintf(
            '%s %s %s %s',
            $binary,
            $options,
            $input,
            \escapeshellarg($output)
        );
    }
}
