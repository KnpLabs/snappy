<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\OptionsValidator;

use KnpLabs\Snappy\Exception\InvalidOptionException;
use KnpLabs\Snappy\OptionsValidator;

class WkHtmlOptionsValidator implements OptionsValidator
{
    private function __construct(
        private array $supportedOptions,
    ) {
    }

    public function validateOptions(array $options): void
    {
        foreach ($options as $option) {
            if ($option === '--toc') {
                throw new InvalidOptionException('The option --toc is not supported by wkhtmltopdf. Use "toc" instead.');
            }
        }
    }

    public static function v1(): self
    {
        return new self([
            'toc',
            'orientation',
        ]);
    }

    public static function v2(): self
    {
        return new self([
            'toc',
            '--orientation',
            '--no-pdf-compression',
        ]);
    }
}
