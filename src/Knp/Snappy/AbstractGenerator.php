<?php

namespace Knp\Snappy;

use Knp\Snappy\Exception\FileAlreadyExistsException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Process\Process;
use Exception;
use LogicException;
use RuntimeException;
use InvalidArgumentException;

/**
 * Base generator class for medias.
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>
 */
abstract class AbstractGenerator implements GeneratorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public array $temporaryFiles = [];

    protected ?string $temporaryFolder = null;

    private ?string $binary;

    private array $options = [];

    private ?array $env;

    private ?int $timeout = null;

    private string $defaultExtension;


    public function __construct(?string $binary, array $options = [], ?array $env = null)
    {
        $this->configure();

        $this->setBinary($binary);
        $this->setOptions($options);
        $this->env = empty($env) ? null : $env;

        if (\is_callable([$this, 'removeTemporaryFiles'])) {
            \register_shutdown_function([$this, 'removeTemporaryFiles']);
        }
    }

    public function __destruct()
    {
        $this->removeTemporaryFiles();
    }

    /**
     * Sets the default extension.
     * Useful when letting Snappy deal with file creation.
     */
    public function setDefaultExtension(string $defaultExtension): static
    {
        $this->defaultExtension = $defaultExtension;

        return $this;
    }

    /**
     * Gets the default extension.
     */
    public function getDefaultExtension(): string
    {
        return $this->defaultExtension;
    }

    /**
     * Sets an option. Be aware that option values are NOT validated and that
     * it is your responsibility to validate user inputs.
     *
     * @param string $name  The option to set
     * @param mixed  $value The value (NULL to unset)
     *
     * @throws InvalidArgumentException
     */
    public function setOption(string $name, mixed $value): static
    {
        if (!\array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(\sprintf('The option \'%s\' does not exist.', $name));
        }

        $this->options[$name] = $value;

        if (null !== $this->logger) {
            $this->logger->debug(\sprintf('Set option "%s".', $name), ['value' => $value]);
        }

        return $this;
    }

    /**
     * Sets the timeout.
     *
     * @param int|null $timeout The timeout to set
     */
    public function setTimeout(?int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets an array of options.
     *
     * @param array<string, mixed> $options An associative array of options as name/value
     */
    public function setOptions(array $options): static
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Returns all the options.
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array|string $input, string $output, array $options = [], bool $overwrite = false): void
    {
        $this->prepareOutput($output, $overwrite);

        $command = $this->getCommand($input, $output, $options);

        $inputFiles = \is_array($input) ? \implode('", "', $input) : $input;

        if (null !== $this->logger) {
            $this->logger->info(\sprintf('Generate from file(s) "%s" to file "%s".', $inputFiles, $output), [
                'command' => $command,
                'env' => $this->env,
                'timeout' => $this->timeout,
            ]);
        }

        try {
            [$status, $stdout, $stderr] = $this->executeCommand($command);
            $this->checkProcessStatus($status, $stdout, $stderr, $command);
            $this->checkOutput($output, $command);
        } catch (Exception $e) {
            if (null !== $this->logger) {
                $this->logger->error(\sprintf('An error happened while generating "%s".', $output), [
                    'command' => $command,
                    'status' => $status ?? null,
                    'stdout' => $stdout ?? null,
                    'stderr' => $stderr ?? null,
                ]);
            }

            throw $e;
        }

        if (null !== $this->logger) {
            $this->logger->info(\sprintf('File "%s" has been successfully generated.', $output), [
                'command' => $command,
                'stdout' => $stdout,
                'stderr' => $stderr,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function generateFromHtml(array|string $html, string $output, array $options = [], bool $overwrite = false): void
    {
        $fileNames = [];
        if (\is_array($html)) {
            foreach ($html as $htmlInput) {
                $fileNames[] = $this->createTemporaryFile($htmlInput, 'html');
            }
        } else {
            $fileNames[] = $this->createTemporaryFile($html, 'html');
        }

        $this->generate($fileNames, $output, $options, $overwrite);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getOutput(array|string $input, array $options = []): string
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $filename, $options);

        return $this->getFileContents($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml(array|string $html, array $options = []): string
    {
        $fileNames = [];
        if (\is_array($html)) {
            foreach ($html as $htmlInput) {
                $fileNames[] = $this->createTemporaryFile($htmlInput, 'html');
            }
        } else {
            $fileNames[] = $this->createTemporaryFile($html, 'html');
        }

        return $this->getOutput($fileNames, $options);
    }

    /**
     * Defines the binary.
     *
     * @param string|null $binary The path/name of the binary
     */
    public function setBinary(?string $binary): static
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Returns the binary.
     */
    public function getBinary(): ?string
    {
        return $this->binary;
    }

    /**
     * Returns the command for the given input and output files.
     *
     * @param array|string $input   The input file
     * @param string $output  The output file
     * @param array        $options An optional array of options that will be used
     *                              only for this command
     */
    public function getCommand(array|string $input, string $output, array $options = []): string
    {
        if (null === $this->binary) {
            throw new LogicException('You must define a binary prior to conversion.');
        }

        $options = $this->mergeOptions($options);

        return $this->buildCommand($this->binary, $input, $output, $options);
    }

    /**
     * Removes all temporary files.
     */
    public function removeTemporaryFiles(): void
    {
        foreach ($this->temporaryFiles as $file) {
            $this->unlink($file);
        }
    }

    public function getTemporaryFolder(): string
    {
        if ($this->temporaryFolder === null) {
            return \sys_get_temp_dir();
        }

        return $this->temporaryFolder;
    }

    public function setTemporaryFolder(string $temporaryFolder): static
    {
        $this->temporaryFolder = $temporaryFolder;

        return $this;
    }

    /**
     * Reset all options to their initial values.
     */
    public function resetOptions(): void
    {
        $this->options = [];
        $this->configure();
    }

    /**
     * This method must configure the media options.
     *
     * @see AbstractGenerator::addOption()
     */
    abstract protected function configure(): void;

    /**
     * Adds an option.
     *
     * @param string $name    The name
     * @param mixed|null $default An optional default value
     *
     * @return $this
     * @throws InvalidArgumentException
     *
     */
    protected function addOption(string $name, mixed $default = null): static
    {
        if (\array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(\sprintf('The option \'%s\' already exists.', $name));
        }

        $this->options[$name] = $default;

        return $this;
    }

    /**
     * Adds an array of options.
     *
     * @param array $options
     */
    protected function addOptions(array $options): static
    {
        foreach ($options as $name => $default) {
            $this->addOption($name, $default);
        }

        return $this;
    }

    /**
     * Merges the given array of options to the instance options and returns
     * the result options array. It does NOT change the instance options.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    protected function mergeOptions(array $options): array
    {
        $mergedOptions = $this->options;

        foreach ($options as $name => $value) {
            if (!\array_key_exists($name, $mergedOptions)) {
                throw new InvalidArgumentException(\sprintf('The option \'%s\' does not exist.', $name));
            }

            $mergedOptions[$name] = $value;
        }

        return $mergedOptions;
    }

    /**
     * Checks the specified output.
     *
     * @param string $output  The output filename
     * @param string $command The generation command
     *
     * @throws RuntimeException if the output file generation failed
     */
    protected function checkOutput(string $output, string $command): void
    {
        // the output file must exist
        if (!$this->fileExists($output)) {
            throw new RuntimeException(\sprintf('The file \'%s\' was not created (command: %s).', $output, $command));
        }

        // the output file must not be empty
        if (0 === $this->filesize($output)) {
            throw new RuntimeException(\sprintf('The file \'%s\' was created but is empty (command: %s).', $output, $command));
        }
    }

    /**
     * Checks the process return status.
     *
     * @param int $status  The exit status code
     * @param string $stdout  The stdout content
     * @param string $stderr  The stderr content
     * @param string $command The run command
     *
     * @throws RuntimeException if the output file generation failed
     */
    protected function checkProcessStatus(int $status, string $stdout, string $stderr, string $command): void
    {
        if (0 !== $status && '' !== $stderr) {
            throw new RuntimeException(\sprintf('The exit status code \'%s\' says something went wrong:' . "\n" . 'stderr: "%s"' . "\n" . 'stdout: "%s"' . "\n" . 'command: %s.', $status, $stderr, $stdout, $command), $status);
        }
    }

    /**
     * Creates a temporary file.
     * The file is not created if the $content argument is null.
     *
     * @param string|null $content   Optional content for the temporary file
     * @param string|null $extension An optional extension for the filename
     *
     * @return string The filename
     */
    protected function createTemporaryFile(string $content = null, string $extension = null): string
    {
        $dir = \rtrim($this->getTemporaryFolder(), \DIRECTORY_SEPARATOR);

        if (!\is_dir($dir)) {
            if (false === @\mkdir($dir, 0777, true) && !\is_dir($dir)) {
                throw new RuntimeException(\sprintf("Unable to create directory: %s\n", $dir));
            }
        } elseif (!\is_writable($dir)) {
            throw new RuntimeException(\sprintf("Unable to write in directory: %s\n", $dir));
        }

        $filename = $dir . \DIRECTORY_SEPARATOR . \uniqid('knp_snappy', true);

        if (null !== $extension) {
            $filename .= '.' . $extension;
        }

        if (null !== $content) {
            \file_put_contents($filename, $content);
        }

        $this->temporaryFiles[] = $filename;

        return $filename;
    }

    /**
     * Builds the command string.
     *
     * @param string $binary  The binary path/name
     * @param array|string $input   Url(s) or file location(s) of the page(s) to process
     * @param string $output  File location to the image-to-be
     * @param array        $options An array of options
     *
     * @return string
     */
    protected function buildCommand(string $binary, array|string $input, string $output, array $options = []): string
    {
        $command = $binary;
        $escapedBinary = \escapeshellarg($binary);
        if (\is_executable($escapedBinary)) {
            $command = $escapedBinary;
        }

        foreach ($options as $key => $option) {
            if (null !== $option && false !== $option) {
                if (true === $option) {
                    // Dont't put '--' if option is 'toc'.
                    if ($key === 'toc') {
                        $command .= ' ' . $key;
                    } else {
                        $command .= ' --' . $key;
                    }
                } elseif (\is_array($option)) {
                    if ($this->isAssociativeArray($option)) {
                        foreach ($option as $k => $v) {
                            $command .= ' --' . $key . ' ' . \escapeshellarg($k) . ' ' . \escapeshellarg($v);
                        }
                    } else {
                        foreach ($option as $v) {
                            $command .= ' --' . $key . ' ' . \escapeshellarg($v);
                        }
                    }
                } else {
                    // Dont't add '--' if option is "cover"  or "toc".
                    if (\in_array($key, ['toc', 'cover'])) {
                        $command .= ' ' . $key . ' ' . \escapeshellarg($option);
                    } elseif (\in_array($key, ['image-dpi', 'image-quality'])) {
                        $command .= ' --' . $key . ' ' . (int) $option;
                    } else {
                        $command .= ' --' . $key . ' ' . \escapeshellarg($option);
                    }
                }
            }
        }

        if (\is_array($input)) {
            foreach ($input as $i) {
                $command .= ' ' . \escapeshellarg($i) . ' ';
            }
            $command .= \escapeshellarg($output);
        } else {
            $command .= ' ' . \escapeshellarg($input) . ' ' . \escapeshellarg($output);
        }

        return $command;
    }

    /**
     * Return true if the array is an associative array
     * and not an indexed array.
     *
     * @param array $array
     */
    protected function isAssociativeArray(array $array): bool
    {
        return (bool) \count(\array_filter(\array_keys($array), 'is_string'));
    }

    /**
     * Executes the given command via shell and returns the complete output as
     * a string.
     *
     * @return array [status, stdout, stderr]
     */
    protected function executeCommand(string $command): array
    {
        if (\method_exists(Process::class, 'fromShellCommandline')) {
            $process = Process::fromShellCommandline($command, null, $this->env);
        } else {
            $process = new Process((array)$command, null, $this->env);
        }

        if (null !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        return [
            $process->getExitCode(),
            $process->getOutput(),
            $process->getErrorOutput(),
        ];
    }

    /**
     * Prepares the specified output.
     *
     * @param string $filename  The output filename
     * @param bool $overwrite Whether to overwrite the file if it already exists
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws FileAlreadyExistsException
     */
    protected function prepareOutput(string $filename, bool $overwrite): void
    {
        $directory = \dirname($filename);

        if ($this->fileExists($filename)) {
            if (!$this->isFile($filename)) {
                throw new InvalidArgumentException(\sprintf('The output file \'%s\' already exists and it is a %s.', $filename, $this->isDir($filename) ? 'directory' : 'link'));
            }
            if (false === $overwrite) {
                throw new FileAlreadyExistsException(\sprintf('The output file \'%s\' already exists.', $filename));
            }
            if (!$this->unlink($filename)) {
                throw new RuntimeException(\sprintf('Could not delete already existing output file \'%s\'.', $filename));
            }
        } elseif (!$this->isDir($directory) && !$this->mkdir($directory)) {
            throw new RuntimeException(\sprintf('The output file\'s directory \'%s\' could not be created.', $directory));
        }
    }

    /**
     * Wrapper for the "file_get_contents" function.
     */
    protected function getFileContents(string $filename): string
    {
        $fileContent = \file_get_contents($filename);

        if (false === $fileContent) {
            throw new RuntimeException(\sprintf('Could not read file \'%s\' content.', $filename));
        }

        return $fileContent;
    }

    /**
     * Wrapper for the "file_exists" function.
     */
    protected function fileExists(string $filename): bool
    {
        return \file_exists($filename);
    }

    /**
     * Wrapper for the "is_file" method.
     */
    protected function isFile(string $filename): bool
    {
        return \strlen($filename) <= \PHP_MAXPATHLEN && \is_file($filename);
    }

    /**
     * Wrapper for the "filesize" function.
     */
    protected function filesize(string $filename): int
    {
        $filesize = \filesize($filename);

        if (false === $filesize) {
            throw new RuntimeException(\sprintf('Could not read file \'%s\' size.', $filename));
        }

        return $filesize;
    }

    /**
     * Wrapper for the "unlink" function.
     */
    protected function unlink(string $filename): bool
    {
        return $this->fileExists($filename) ? \unlink($filename) : false;
    }

    /**
     * Wrapper for the "is_dir" function.
     */
    protected function isDir(string $filename): bool
    {
        return \is_dir($filename);
    }

    /**
     * Wrapper for the mkdir function.
     */
    protected function mkdir(string $pathname): bool
    {
        return \mkdir($pathname, 0777, true);
    }
}
