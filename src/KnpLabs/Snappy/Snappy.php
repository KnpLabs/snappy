<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use Exception;
use InvalidArgumentException;
use KnpLabs\Snappy\Exception\FileAlreadyExistsException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Snappy
{
    public function __construct(
        private Adapter $adapter,
        private ProcessRunner $processRunner,
        private Filesystem $filesystem,
        private ?LoggerInterface $logger = null,
        private ?string $env = null,
    ) {
    }

    public function generate(
        string|array $input,
        string $outputPath,
        array $options = [],
        bool $overwrite = false
    ): void {
        $this->prepareOutput($outputPath, $overwrite);

        $command = $this->adapter->getCommand($input, $outputPath, $options);

        if (null !== $this->logger) {
            $inputFiles = \is_array($input) ? \implode(', ', $input) : $input;

            $this->logger->info(\sprintf('Generate from file(s) "%s" to file "%s".', $inputFiles, $outputPath), [
                'command' => $command,
                'env' => $this->env,
                // 'timeout' => $timeout,
            ]);
        }

        $processOutput = $this->processRunner->executeCommand($command);

        try {
            $processOutput->ensureSuccessful();
            $this->processRunner->checkOutput($this->filesystem, $outputPath, $command);
        } catch (Exception $e) {
            $this->logger->error(sprintf('An error happened while generating "%s".', $outputPath), [
                'command' => $processOutput->command,
                'status'  => $processOutput->exitCode,
                'stdout'  => $processOutput->stdout,
                'stderr'  => $processOutput->stderr,
            ]);

            throw $e;
        }

        $this->logger->info(sprintf('File "%s" has been successfully generated.', $outputPath), [
            'command' => $processOutput->command,
            'stdout'  => $processOutput->stdout,
            'stderr'  => $processOutput->stderr,
        ]);
    }

    public function generateFromHtml(
        string $html,
        string $outputPath,
        array $options = [],
        bool $overwrite = false
    ): void {
        //$this->adapter->generateFromHtml($html, $outputPath, $options);
    }

    /**
     * Prepares the specified output.
     *
     * @param string $filename  The output filename
     * @param bool   $overwrite Whether to overwrite the file if it already
     *                          exist
     *
     * @throws FileAlreadyExistsException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function prepareOutput(string $filename, bool $overwrite)
    {
        $directory = \dirname($filename);

        if ($this->filesystem->fileExists($filename)) {
            if (!$this->filesystem->isFile($filename)) {
                throw new InvalidArgumentException(\sprintf(
                    'The output file \'%s\' already exists and it is a %s.',
                    $filename,
                    $this->filesystem->isDir($filename)
                        ? 'directory'
                        : 'link')
                    );
            }
            if (false === $overwrite) {
                throw new FileAlreadyExistsException(\sprintf('The output file \'%s\' already exists.', $filename));
            }
            if (!$this->filesystem->unlink($filename)) {
                throw new RuntimeException(\sprintf('Could not delete already existing output file \'%s\'.', $filename));
            }
        } elseif (!$this->filesystem->isDir($directory) && !$this->filesystem->mkdir($directory)) {
            throw new RuntimeException(\sprintf('The output file\'s directory \'%s\' could not be created.', $directory));
        }
    }
}
