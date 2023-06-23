<?php

declare(strict_types=1);

namespace KnpLabs\Core\Bridge;

use KnpLabs\Snappy\Core\FileToPdf;
use KnpLabs\Snappy\Core\StringToPdf;
use Psr\Http\Message\StreamInterface;

final class FromStringToFileToPdf implements StringToPdf
{
    public function __construct(private FileToPdf $fileToPdf)
    {
    }

    public function generateFromString(string $html, \ArrayAccess|array $options = []): StreamInterface
    {
        $path = tempnam(sys_get_temp_dir(), 'snappy_');

        try {
            file_put_contents($path, $html);

            $file = new \SplFileInfo($path);

            $stream = $this->fileToPdf->generateFromFile($file, $options);
        } finally {
            unlink($path);
        }

        return $stream;
    }
}
