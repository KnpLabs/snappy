<?php

namespace KnpLabs\Snappy\Core;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface UriToPdf
{
    public function generateFromUri(UriInterface $url, \ArrayAccess|array $options = []): StreamInterface;
}
