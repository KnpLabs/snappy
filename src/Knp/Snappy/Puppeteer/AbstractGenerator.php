<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

use Knp\Snappy\Exception\GenerationFailed;
use Knp\Snappy\Filesystem;
use Knp\Snappy\Generator;
use Knp\Snappy\LocalGenerator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Abstract Puppeteer generator.
 *
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
abstract class AbstractGenerator implements Generator, LocalGenerator
{
    /** @var string */
    private $nodePath;

    /** @var Filesystem */
    private $filesystem;

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $env;

    /** @var int */
    private $timeout = false;

    private $options = [
        'emulateMedia'      => null,
        'cookies'           => null,
        'extraHTTPHeaders'  => null,
        'javaScriptEnabled' => null,
        'userAgent'         => null,
        'viewport'          => null,
    ];

    public function __construct(array $options = [], $nodePath = null, array $env = [])
    {
        $this->configure();
        $this->setOptions($options);
        $this->nodePath = $nodePath;
        $this->env = !empty($env) ? $env : null;
        $this->filesystem = new Filesystem();
        $this->logger = new NullLogger();
    }

    /**
     * This method must configure the media options.
     *
     * @see AbstractGenerator::addOption()
     */
    abstract protected function configure();

    /**
     * Define the action used, can be 'pdf' or 'screenshot'.
     *
     * @return string
     */
    abstract protected function getAction() : string;

    /**
     * Get the default extension for the temporary files.
     *
     * @return string
     */
    abstract protected function getDefaultExtension(): string;

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the Node Modules path where Puppeteer is installed.
     *
     * @param string $nodePath The path to the node_modules dir
     */
    public function setNodePath(string $nodePath)
    {
        $this->nodePath = $nodePath;
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
     * {@inheritdoc}
     */
    public function generate($input, string $output, array $options = [], bool $overwrite = false)
    {
        $options = array_filter($this->mergeOptions($options));
        $command = $this->buildCommand($input, $output, $options);
        $process = new Process($command, null, $this->env, null, $this->timeout);

        $this->logger->info(sprintf('Run puppeteer command: "%s".', $command), [
            'command' => $command,
            'env'     => $this->env,
            'timeout' => $this->timeout,
        ]);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $this->logger->error(sprintf('Puppeteer process failed during execution.'), [
                'command'  => $command,
                'env'      => $this->env,
                'timeout'  => $this->timeout,
                'exitCode' => $process->getExitCode(),
                'stdout'   => $process->getOutput(),
                'stderr'   => $process->getErrorOutput(),
            ]);

            throw new GenerationFailed('Generation failed', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false)
    {
        $input = $this->filesystem->createTemporaryFile($html, 'html');

        $this->filesystem->prepareOutput($output, $overwrite);

        $this->generate(sprintf('file://%s', $input), $output, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput($input, array $options = [])
    {
        $output = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $output, $options);

        return $this->filesystem->getFileContents($output);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml($html, array $options = [])
    {
        $input = $this->filesystem->createTemporaryFile($html, 'html');
        $output = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate(sprintf('file://%s', $input), $output, $options);

        return $this->filesystem->getFileContents($output);
    }

    /**
     * @param string $input   URI of the input document used
     * @param string $output  Path to the output file
     * @param array  $options Options and arguments to pass to chrome (empty/false/null options are ignored)
     *
     * @return string
     */
    protected function buildCommand(string $input, string $output, array $options): string
    {
        if ($this->nodePath && is_dir($this->nodePath)) {
            $nodePath = escapeshellarg($this->nodePath);
        } else {
            $nodePath = '`npm root -g`';    // Detect root node path
        }

        return implode(' ', [
            'NODE_PATH=' . $nodePath,
            'node',
            escapeshellarg(dirname(dirname(dirname(dirname(__DIR__)))) . '//resources/puppeteer.js'),
            escapeshellarg($this->getAction()),
            escapeshellarg($input),
            escapeshellarg($output),
            escapeshellarg(json_encode($options)),
        ]);
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
}
