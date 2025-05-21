<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Replace $name with $value in header and footer.
 */
final class Replace extends ExtraOption
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name, string $value)
    {
        parent::__construct(
            repeatable: true,
            command: ['--replace', $name, $value]
        );
    }
}
