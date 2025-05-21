<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Set viewport size if you have custom scrollbars or css attribute overflow to emulate window size.
 */
final class ViewportSize extends ExtraOption
{
    /**
     * @param non-empty-string $viewSize
     */
    public function __construct(string $viewSize)
    {
        parent::__construct(
            repeatable: false,
            command: ['--viewport-size', $viewSize],
        );
    }
}
