<?php

namespace KnpLabs\Snappy\Core;

use Psr\Http\Message\StreamInterface;

interface StringToPdf
{
    public function generateFromString(string $html, \ArrayAccess|array $options = []): StreamInterface;
}
