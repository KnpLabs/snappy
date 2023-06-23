<?php

namespace KnpLabs\Snappy\Core;

use ArrayAccess;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

interface StringToPdf
{
    public function generate(string $html, ArrayAccess|array $options = []): StreamInterface;
}
