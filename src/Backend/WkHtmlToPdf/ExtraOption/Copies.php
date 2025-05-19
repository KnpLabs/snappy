<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Number of copies to print into the pdf file.
 *
 * Default: 1
 */
final class Copies extends ExtraOption
{
    /**
     * @param positive-int $number
     */
    public function __construct(int $number)
    {
        parent::__construct(
            repeatable: false,
            command: ['--copies', (string) $number],
        );
    }
}
