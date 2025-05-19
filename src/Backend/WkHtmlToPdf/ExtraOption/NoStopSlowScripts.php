<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Do not Stop slow running javascripts.
 */
final class NoStopSlowScripts extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--no-stop-slow-scripts']
        );
    }
}
