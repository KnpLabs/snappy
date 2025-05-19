<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Post an additional file.
 */
final class PostFile extends ExtraOption
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $path
     */
    public function __construct(string $name, string $path)
    {
        parent::__construct(
            repeatable: true,
            command: ['--post-file', $name, $path]
        );
    }
}
