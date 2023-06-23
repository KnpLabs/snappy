<?php

declare(strict_types=1);

namespace KnpLabs\Backend\WkHtmlToPdf;

use ArrayAccess;
use KnpLabs\Core\FileToPdf;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final class WkHtmlToPdf implements FileToPdf
{
    public function generate(SplFileInfo $file, ArrayAccess|array $options = []): StreamInterface
    {
    }
}
