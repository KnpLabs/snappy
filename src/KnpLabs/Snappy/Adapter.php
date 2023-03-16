<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

interface Adapter
{
    // public function generateFromHtml(string $html, string $outputPath, array $options): void;
    public function getCommand(string|array $input, string $outputPath, array $options): string;
    public function generate(string $input, array $options): string;
    public function generateFromHtml(string $html, array $options): string;
}
