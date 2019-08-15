<?php

declare(strict_types=1);

namespace Knp\Snappy\Wkhtmltox;

use Knp\Snappy\Exception;
use Knp\Snappy\Filesystem;
use Knp\Snappy\LocalGenerator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;

/**
 * Base generator class for medias.
 *
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>
 */
abstract class AbstractGenerator implements LocalGenerator, LoggerAwareInterface
{
    private $binary;
    private $options = [];
    private $env;
    private $timeout = false;
    private $defaultExtension;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Filesystem */
    private $filesystem;

    /**
     * @param string $binary
     * @param array  $options
     * @param array  $env
     */
    public function __construct($binary, array $options = [], array $env = null)
    {
        $this->env = empty($env) ? null : $env;
        $this->logger = new NullLogger();
        $this->filesystem = new Filesystem();

        $this->configure();
        $this->setBinary($binary);
        $this->setOptions($options);
    }

    /**
     * Set the logger to use to log debugging data.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * This method must configure the media options.
     *
     * @see AbstractGenerator::addOption()
     */
    abstract protected function configure();

    /**
     * Sets the default extension.
     * Useful when letting Snappy deal with file creation.
     *
     * @param string $defaultExtension
     */
    public function setDefaultExtension($defaultExtension)
    {
        $this->defaultExtension = $defaultExtension;
    }

    /**
     * Gets the default extension.
     *
     * @return $string
     */
    public function getDefaultExtension()
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
     * @throws \InvalidArgumentException
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option \'%s\' does not exist.', $name));
        }

        $this->options[$name] = $value;

        $this->logger->debug(sprintf('Set option "%s" to "%s".', $name, var_export($value, true)));
    }

    /**
     * Sets the timeout.
     *
     * @param int $timeout The timeout to set
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Sets an array of options.
     *
     * @param array $options An associative array of options as name/value
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Returns all the options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($input, string $output, array $options = [], bool $overwrite = false)
    {
        if (null === $this->binary) {
            throw new Exception\MissingBinary();
        }

        $this->filesystem->prepareOutput($output, $overwrite);

        $command = $this->getCommand($input, $output, $options);

        $inputFiles = is_array($input) ? implode('", "', $input) : $input;

        $this->logger->info(sprintf('Generate from file(s) "%s" to file "%s".', $inputFiles, $output), [
            'command' => $command,
            'env'     => $this->env,
            'timeout' => $this->timeout,
        ]);

        try {
            list($status, $stdout, $stderr) = $this->executeCommand($command);
            $this->checkProcessStatus($status, $stdout, $stderr, $command);
            $this->checkOutput($output, $command);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('An error happened while generating "%s".', $output), [
                'command' => $command,
                'status'  => isset($status) ? $status : null,
                'stdout'  => isset($stdout) ? $stdout : null,
                'stderr'  => isset($stderr) ? $stderr : null,
            ]);

            throw $e;
        }

        $this->logger->info(sprintf('File "%s" has been successfully generated.', $output), [
            'command' => $command,
            'stdout'  => $stdout,
            'stderr'  => $stderr,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false)
    {
        $fileNames = [];
        if (is_array($html)) {
            foreach ($html as $htmlInput) {
                $fileNames[] = $this->filesystem->createTemporaryFile($htmlInput, 'html');
            }
        } else {
            $fileNames[] = $this->filesystem->createTemporaryFile($html, 'html');
        }

        $this->generate($fileNames, $output, $options, $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput($input, array $options = [])
    {
        $filename = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $filename, $options);

        $result = $this->filesystem->getFileContents($filename);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml($html, array $options = [])
    {
        $fileNames = [];
        if (is_array($html)) {
            foreach ($html as $htmlInput) {
                $fileNames[] = $this->filesystem->createTemporaryFile($htmlInput, 'html');
            }
        } else {
            $fileNames[] = $this->filesystem->createTemporaryFile($html, 'html');
        }

        $result = $this->getOutput($fileNames, $options);

        return $result;
    }

    /**
     * Defines the binary.
     *
     * @param string $binary The path/name of the binary
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;
    }

    /**
     * Returns the binary.
     *
     * @return string
     */
    public function getBinary()
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
    public function getCommand($input, $output, array $options = [])
    {
        $options = $this->mergeOptions($options);

        return $this->buildCommand($this->binary, $input, $output, $options);
    }

    /**
     * Adds an option.
     *
     * @param string $name    The name
     * @param mixed  $default An optional default value
     *
     * @throws \InvalidArgumentException
     */
    protected function addOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option \'%s\' already exists.', $name));
        }

        $this->options[$name] = $default;
    }

    /**
     * Adds an array of options.
     *
     * @param array $options
     */
    protected function addOptions(array $options)
    {
        foreach ($options as $name => $default) {
            $this->addOption($name, $default);
        }
    }

    /**
     * Merges the given array of options to the instance options and returns
     * the result options array. It does NOT change the instance options.
     *
     * @param array $options
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function mergeOptions(array $options)
    {
        $mergedOptions = $this->options;

        foreach ($options as $name => $value) {
            if (!array_key_exists($name, $mergedOptions)) {
                throw new \InvalidArgumentException(sprintf('The option \'%s\' does not exist.', $name));
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
     * @throws Exception\GenerationFailed When the output file does not exist or is empty.
     */
    protected function checkOutput($output, $command)
    {
        // the output file must exist
        if (!$this->filesystem->exists($output)) {
            throw new Exception\GenerationFailed(sprintf(
                'The file \'%s\' was not created (command: %s).',
                $output, $command
            ));
        }

        // the output file must not be empty
        if (0 === $this->filesystem->getFileSize($output)) {
            throw new Exception\GenerationFailed(sprintf(
                'The file \'%s\' was created but is empty (command: %s).',
                $output, $command
            ));
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
     * @throws Exception\GenerationFailed When the process failed with an error message
     */
    protected function checkProcessStatus($status, $stdout, $stderr, $command)
    {
        if (0 !== $status and '' !== $stderr) {
            throw new Exception\GenerationFailed(sprintf(
                'The exit status code \'%s\' says something went wrong:' . "\n"
                . 'stderr: "%s"' . "\n"
                . 'stdout: "%s"' . "\n"
                . 'command: %s.',
                $status, $stderr, $stdout, $command
            ));
        }
    }

    /**
     * Builds the command string.
     *
     * @param string       $binary  The binary path/name
     * @param string/array $input   Url(s) or file location(s) of the page(s) to process
     * @param string       $output  File location to the image-to-be
     * @param array        $options An array of options
     *
     * @return string
     */
    protected function buildCommand($binary, $input, $output, array $options = [])
    {
        $command = $binary;
        $escapedBinary = escapeshellarg($binary);
        if (is_executable($escapedBinary)) {
            $command = $escapedBinary;
        }

        foreach ($options as $key => $option) {
            if (null !== $option && false !== $option) {
                if (true === $option) {
                    // Dont't put '--' if option is 'toc'.
                    if ($key == 'toc') {
                        $command .= ' ' . $key;
                    } else {
                        $command .= ' --' . $key;
                    }
                } elseif (is_array($option)) {
                    if ($this->isAssociativeArray($option)) {
                        foreach ($option as $k => $v) {
                            $command .= ' --' . $key . ' ' . escapeshellarg(strval($k)) . ' ' . escapeshellarg(strval($v));
                        }
                    } else {
                        foreach ($option as $v) {
                            $command .= ' --' . $key . ' ' . escapeshellarg(strval($v));
                        }
                    }
                } else {
                    // Dont't add '--' if option is "cover"  or "toc".
                    if (in_array($key, ['toc', 'cover'])) {
                        $command .= ' ' . $key . ' ' . escapeshellarg(strval($option));
                    } elseif (in_array($key, ['image-dpi', 'image-quality'])) {
                        $command .= ' --' . $key . ' ' . (int) $option;
                    } else {
                        $command .= ' --' . $key . ' ' . escapeshellarg(strval($option));
                    }
                }
            }
        }

        if (is_array($input)) {
            foreach ($input as $i) {
                $command .= ' ' . escapeshellarg(strval($i)) . ' ';
            }
            $command .= escapeshellarg(strval($output));
        } else {
            $command .= ' ' . escapeshellarg(strval($input)) . ' ' . escapeshellarg(strval($output));
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
    protected function isAssociativeArray(array $array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Executes the given command via shell and returns the complete output as
     * a string.
     *
     * @param string $command
     *
     * @return array(status, stdout, stderr)
     */
    protected function executeCommand($command)
    {
        $process = new Process($command, null, $this->env);

        if (false !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        return [
            $process->getExitCode(),
            $process->getOutput(),
            $process->getErrorOutput(),
        ];
    }
}
