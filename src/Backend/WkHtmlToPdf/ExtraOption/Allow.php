<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Allow the file or files from the specified folder to be loaded.
 */
final class Allow extends ExtraOption
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(
            repeatable: true,
            command: ['--allow', $path]
        );
    }
}
