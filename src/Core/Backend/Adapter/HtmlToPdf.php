<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend\Adapter;

use KNPLabs\Snappy\Core\Backend\Adapter;
use Psr\Http\Message\StreamInterface;

interface HtmlToPdf extends Adapter
{
    public function generateFromHtml(string $html): StreamInterface;
}
