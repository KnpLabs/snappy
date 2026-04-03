<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Ensure all compositor stages complete before rendering begins.
 */
final class RunAllCompositorStagesBeforeDraw extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--run-all-compositor-stages-before-draw']
        );
    }
}
