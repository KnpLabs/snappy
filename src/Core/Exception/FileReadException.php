<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Exception;

use KNPLabs\Snappy\Core\Exception;

final class FileReadException extends Exception
{
    public function __construct(\SplFileInfo $file)
    {
        file_exists($file->getPathname())
            ? parent::__construct(\sprintf("File %s can't be read.", $file->getPathname()))
            : parent::__construct(\sprintf('File %s not found.', $file->getPathname()));
    }
}
