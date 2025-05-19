<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * The title of the generated pdf file
 * Default: the title of the first document is used if not specified.
 */
final class Title extends ExtraOption
{
    /**
     * @param non-empty-string $title
     */
    public function __construct(string $title)
    {
        parent::__construct(
            repeatable: false,
            command: ['--title', $title]
        );
    }
}
