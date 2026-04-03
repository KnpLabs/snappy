<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Fast-forward JavaScript timers and animations to given virtual time budget.
 */
final class VirtualTimeBudget extends ExtraOption
{
    /**
     * @param int<0, max> $milliseconds
     */
    public function __construct(int $milliseconds)
    {
        parent::__construct(
            repeatable: false,
            command: ['--virtual-time-budget=' . $milliseconds]
        );
    }
}
