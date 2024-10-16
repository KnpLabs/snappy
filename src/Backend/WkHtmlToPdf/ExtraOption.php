<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

interface ExtraOption
{
    /** @return array<float|int|string> */
    public function compile(): array;
}
