<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Set the viewport size for PDF rendering.
 */
final class WindowSize extends ExtraOption
{
    /**
     * @param non-empty-string $size Format: "width,height" (e.g., "1920,1080")
     */
    public function __construct(string $size)
    {
        parent::__construct(
            repeatable: false,
            command: ['--window-size', $size]
        );
    }
}
