<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use RuntimeException;
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

    public function checkOutput(
        Filesystem $filesystem,
        string $output,
        string $command
    ): void {
        // the output file must exist
        if (!$filesystem->fileExists($output)) {
            throw new RuntimeException(\sprintf(
                'The file \'%s\' was not created (command: %s).',
                $output,
                $command
            ));
        }

        // the output file must not be empty
        if (0 === $filesystem->filesize($output)) {
            throw new RuntimeException(\sprintf(
                'The file \'%s\' was created but is empty (command: %s).',
                $output,
                $command
            ));
        }
    }
}
