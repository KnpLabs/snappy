<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

interface ExtraOption {
    /** @return array<string|int|float> */
    public function compile(): array;
}
