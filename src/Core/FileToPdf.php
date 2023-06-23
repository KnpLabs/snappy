<?php

namespace KnpLabs\Snappy\Core;

use ArrayAccess;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

interface FileToPdf
{
    public function generate(SplFileInfo $file, ArrayAccess|array $options = []): StreamInterface;
}
