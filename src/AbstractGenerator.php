<?php

namespace Knp\Snappy;

use Knp\Snappy\Exception\FileAlreadyExistsException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
    /**
     * @var array
     */
    public $temporaryFiles = [];

    /**
     * @var string
     */
    protected $temporaryFolder;

    /**
     * @var null|string
     */
    private $binary;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var null|array
     */
    private $env;

    /**
     * @var null|int
     */
    private $timeout;

    /**
     * @var string
     */
    private $defaultExtension;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param null|string $binary
     * @param array       $options
     * @param null|array  $env
     */
    public function __construct(string $binary = null, array $options = [], array $env = null)
    {
        $this->configure();

        $this->logger = new NullLogger();
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
     * Set the logger to use to log debugging data.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Sets the default extension.
     * Useful when letting Snappy deal with file creation.
     *
     * @param string $defaultExtension
     */
    public function setDefaultExtension(string $defaultExtension): self
    {
        $this->defaultExtension = $defaultExtension;

        return $this;
    }

    /**
     * Gets the default extension.
     *
     * @return string
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
    public function setOption(string $name, $value): self
    {
        if (!\array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(\sprintf('The option \'%s\' does not exist.', $name));
        }

        $this->options[$name] = $value;

        $this->logger->debug(\sprintf('Set option "%s".', $name), ['value' => $value]);

        return $this;
    }

    /**
     * Sets the timeout.
     *
     * @param null|int $timeout The timeout to set
     */
    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets an array of options.
     *
     * @param array $options An associative array of options as name/value
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Returns all the options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($input, string $output, array $options = [], bool $overwrite = false): void
    {
        $this->prepareOutput($output, $overwrite);

        $command = $this->getCommand($input, $output, $options);

        $inputFiles = \is_array($input) ? \implode('", "', $input) : $input;

        $this->logger->info(\sprintf('Generate from file(s) "%s" to file "%s".', $inputFiles, $output), [
            'command' => $command,
            'env' => $this->env,
            'timeout' => $this->timeout,
        ]);

        try {
            list($status, $stdout, $stderr) = $this->executeCommand($command);
            $this->checkProcessStatus($status, $stdout, $stderr, $command);
            $this->checkOutput($output, $command);
        } catch (Exception $e) {
            $this->logger->error(\sprintf('An error happened while generating "%s".', $output), [
                'command' => $command,
                'status' => $status ?? null,
                'stdout' => $stdout ?? null,
                'stderr' => $stderr ?? null,
            ]);

            throw $e;
        }

        $this->logger->info(\sprintf('File "%s" has been successfully generated.', $output), [
            'command' => $command,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false): void
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
     */
    public function getOutput($input, array $options = []): string
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $filename, $options);

        return $this->getFileContents($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml($html, array $options = []): string
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
     * @param null|string $binary The path/name of the binary
     */
    public function setBinary(?string $binary): self
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Returns the binary.
     *
     * @return null|string
     */
    public function getBinary(): ?string
    {
        return $this->binary;
    }

    /**
     * Returns the command for the given input and output files.
     *
     * @param array|string $input   The input file
     * @param string       $output  The ouput file
     * @param array        $options An optional array of options that will be used
     *                              only for this command
     *
     * @return string
     */
    public function getCommand($input, string $output, array $options = []): string
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

    /**
     * Get TemporaryFolder.
     *
     * @return string
     */
    public function getTemporaryFolder(): string
    {
        if ($this->temporaryFolder === null) {
            return \sys_get_temp_dir();
        }

        return $this->temporaryFolder;
    }

    /**
     * Set temporaryFolder.
     *
     * @param string $temporaryFolder
     *
     * @return $this
     */
    public function setTemporaryFolder(string $temporaryFolder): self
    {
        $this->temporaryFolder = $temporaryFolder;

        return $this;
    }

    /**
     * Reset all options to their initial values.
     *
     * @return void
     */
    public function resetOptions(): void
    {
        $this->options = [];
        $this->configure();
    }

    /**
     * This method must configure the media options.
     *
     * @return void
     *
     * @see AbstractGenerator::addOption()
     */
    abstract protected function configure(): void;

    /**
     * Adds an option.
     *
     * @param string $name    The name
     * @param mixed  $default An optional default value
     *
     * @throws InvalidArgumentException
     */
    protected function addOption(string $name, $default = null): self
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
    protected function addOptions(array $options): self
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
     * @param int    $status  The exit status code
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
     * @param null|string $content   Optional content for the temporary file
     * @param null|string $extension An optional extension for the filename
     *
     * @return string The filename
     */
    protected function createTemporaryFile(?string $content = null, ?string $extension = null): string
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
     * @param string       $binary  The binary path/name
     * @param array|string $input   Url(s) or file location(s) of the page(s) to process
     * @param string       $output  File location to the image-to-be
     * @param array        $options An array of options
     *
     * @return string
     */
    protected function buildCommand(string $binary, $input, string $output, array $options = []): string
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
     *
     * @return bool
     */
    protected function isAssociativeArray(array $array): bool
    {
        return (bool) \count(\array_filter(\array_keys($array), 'is_string'));
    }

    /**
     * Executes the given command via shell and returns the complete output as
     * a string.
     *
     * @param string $command
     *
     * @return array [status, stdout, stderr]
     */
    protected function executeCommand(string $command): array
    {
        if (\method_exists(Process::class, 'fromShellCommandline')) {
            $process = Process::fromShellCommandline($command, null, $this->env);
        } else {
            $process = new Process($command, null, $this->env);
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
     * @param bool   $overwrite Whether to overwrite the file if it already
     *                          exist
     *
     * @throws FileAlreadyExistsException
     * @throws RuntimeException
     * @throws InvalidArgumentException
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
     *
     * @param string $filename
     *
     * @return string
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
     *
     * @param string $filename
     *
     * @return bool
     */
    protected function fileExists(string $filename): bool
    {
        return \file_exists($filename);
    }

    /**
     * Wrapper for the "is_file" method.
     *
     * @param string $filename
     *
     * @return bool
     */
    protected function isFile(string $filename): bool
    {
        return \strlen($filename) <= \PHP_MAXPATHLEN && \is_file($filename);
    }

    /**
     * Wrapper for the "filesize" function.
     *
     * @param string $filename
     *
     * @return int
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
     *
     * @param string $filename
     *
     * @return bool
     */
    protected function unlink(string $filename): bool
    {
        return $this->fileExists($filename) ? \unlink($filename) : false;
    }

    /**
     * Wrapper for the "is_dir" function.
     *
     * @param string $filename
     *
     * @return bool
     */
    protected function isDir(string $filename): bool
    {
        return \is_dir($filename);
    }

    /**
     * Wrapper for the mkdir function.
     *
     * @param string $pathname
     *
     * @return bool
     */
    protected function mkdir(string $pathname): bool
    {
        return \mkdir($pathname, 0777, true);
    }
}
