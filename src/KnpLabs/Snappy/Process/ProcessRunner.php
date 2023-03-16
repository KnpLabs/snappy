<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Process;

use Symfony\Component\Process\Process;

class ProcessRunner
{
    public function __construct(
        private ?int $timeout = null,
    ) {
    }

    public function executeCommand(string $command): ProcessOutput
    {
        $process = Process::fromShellCommandline($command);

        if (null !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        return new ProcessOutput(
            $command,
            $process->getExitCode(),
            $process->getOutput(),
            $process->getErrorOutput(),
        );
    }
}
