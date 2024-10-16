<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class DumpDefaultTocXsl implements ExtraOption
{
    public function __construct() {}

    public function compile(): array
    {
        return ['--dump-default-toc-xsl'];
    }
}
