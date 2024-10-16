<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\Option;

final class CollateOption implements Option
{
    public function compile(): array
    {
        return ['--collate'];
    }
}
