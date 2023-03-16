
<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use RuntimeException;

class ProcessOutput
{
    public function __construct(
        public readonly string $command,
        public readonly int $exitCode,
        public readonly string $stdout,
        public readonly string $stderr,
    ) {
    }

    public function ensureSuccessful(): void
    {
        if (0 === $this->exitCode || '' === $this->stderr) {
            return;
        }

        throw new RuntimeException(\sprintf(
            'The exit status code \'%s\' says something went wrong:' . "\n" . 'stderr: "%s"' . "\n" . 'stdout: "%s". ' . "\n" . 'command: "%s".',
            $this->exitCode,
            $this->stderr,
            $this->stdout,
            $this->command,
        ));
    }
}
