<?php

declare(strict_types=1);

namespace KnpLabs\Core\Bridge;

use KnpLabs\Snappy\Core\FileToPdf;
use KnpLabs\Snappy\Core\StringToPdf;
use Psr\Http\Message\StreamInterface;

final class FromFileToStringToPdf implements FileToPdf
{
    public function __construct(private StringToPdf $stringToPdf)
    {
    }

    public function generateFromFile(\SplFileInfo $file, \ArrayAccess|array $options = []): StreamInterface
    {
        return $this->stringToPdf->generateFromString(
            file_get_contents($file->getPathname()),
            $options,
        );
    }
}
