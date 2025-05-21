<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Adds a html footer.
 */
final class FooterHtml extends ExtraOption
{
    /**
     * @param non-empty-string $url
     */
    public function __construct(string $url)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-html', $url]
        );
    }
}
