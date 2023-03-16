<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Adapter;

use KnpLabs\Snappy\Adapter;
use KnpLabs\Snappy\Options;
use KnpLabs\Snappy\OptionsFactory;

class ChromeHeadless implements Adapter
{
    private Options $defaultOptions;

    public function __construct(
        private readonly string $binary,
        private readonly OptionsFactory $optionsFactory,
        private readonly string $mode = 'pdf',
        array $defaultOptions = []
    ) {
        $this->defaultOptions = $this->buildOptions([
            ...$defaultOptions,
            'headless' => true,
            //'disable-gpu' => true,
        ]);
    }

    private function buildOptions(array $options): Options
    {
        return $this->optionsFactory->create($options);
    }

    public function getCommand(string|array $input, string $outputPath, array $options): string
    {
        $extraOptions = $this->mode === 'pdf' ? [
            'print-to-pdf' => $outputPath,
        ] : [
            'screenshot' => $outputPath,
            'window-size' => '1920,1080',
        ];

        $options = $this->buildOptions(array_merge($options, $extraOptions));
        $options = $this->defaultOptions->merge($options);

        return sprintf(
            "%s %s %s",
            $this->binary,
            $options,
            $input
        );
    }

    public function generateFromHtml(string $html, string $outputPath, array $options): void
    {
    }
}
