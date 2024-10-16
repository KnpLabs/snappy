<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Exception;

use KNPLabs\Snappy\Core\Exception;

final class StreamDetachedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Stream is detached.');
    }
}
