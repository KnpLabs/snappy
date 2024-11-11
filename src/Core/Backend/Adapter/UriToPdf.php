<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend\Adapter;

use KNPLabs\Snappy\Core\Backend\Adapter;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface UriToPdf extends Adapter
{
    public function generateFromUri(UriInterface $url): StreamInterface;
}
