<?php

namespace KnpLabs\Snappy\Core;

use Psr\Http\Message\StreamInterface;

interface FileToPdf
{
    public function generateFromFile(\SplFileInfo $file, \ArrayAccess|array $options = []): StreamInterface;
}
