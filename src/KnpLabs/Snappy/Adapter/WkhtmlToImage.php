<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Adapter;

use KnpLabs\Snappy\Adapter;
use KnpLabs\Snappy\Options;
use KnpLabs\Snappy\OptionsFactory;
use LogicException;

class WkhtmlToImage implements Adapter
{
    private Options $defaultOptions;

    public function __construct(
        private string $binary,
        private OptionsFactory $optionsFactory,
        array $defaultOptions = []
    ) {
        $this->defaultOptions = $this->buildOptions($defaultOptions);
    }

    private function buildOptions(array $options): Options
    {
        return $this->optionsFactory->create($options);
    }

    public function getCommand(array|string $input, string $output, array $options): string
    {
        if (null === $this->binary) {
            throw new LogicException('You must define a binary prior to conversion.');
        }

        $options = $this->buildOptions($options);
        $options = $this->defaultOptions->merge($options);

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

    public function generateFromHtml(string $html, string $outputPath, array $options): void { }
}
