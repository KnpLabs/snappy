<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Disable GPU hardware acceleration for stability in headless environments.
 */
final class DisableGpu extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--disable-gpu']
        );
    }
}
