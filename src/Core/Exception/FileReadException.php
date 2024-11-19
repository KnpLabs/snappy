<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Exception;

use KNPLabs\Snappy\Core\Exception;

final class FileReadException extends Exception
{
    public function __construct(\SplFileInfo $file)
    {
        file_exists($file->getPathname())
            ? parent::__construct("File {$file->getPathname()} can't be read.")
            : parent::__construct("File {$file->getPathname()} not found.");
    }
}
