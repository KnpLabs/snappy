<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Core\Backend\Adapter;

use KNPLabs\Snappy\Core\Backend\Adapter;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

interface HtmlFileToPdf extends Adapter
{
    public function generateFromHtmlFile(SplFileInfo $file): StreamInterface;
}
