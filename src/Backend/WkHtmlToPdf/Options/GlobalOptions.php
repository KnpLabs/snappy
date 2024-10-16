<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\OptionGroup;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions\CollateOption;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions\CookieJarOption;
use KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions\NoCollateOption;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;

final class GlobalOptions extends OptionGroup
{
    public function __construct(
        public readonly ?CollateOption $collate,
        public readonly ?NoCollateOption $noCollate,
        public readonly ?CookieJarOption $cookieJar,
        public readonly ?PageOrientation $pageOrientation,
    ) {}
}
