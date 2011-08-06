<?php

namespace Knp\Snappy;

/**
 * Base generator class for medias
 *
 * @package Snappy
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    private $binary;
    private $options = array();
    private $defaultExtension;

    /**
     * Constructor
     *
     * @param  string $binary
     * @param  array  $options
     */
    public function __construct($binary, array $options = array())
    {
        $this->configure();

        $this->setBinary($binary);
        $this->setOptions($options);
    }

    /**
     * This method must configure the media options
     *
     * @see AbstractGenerator::addOption()
     */
    abstract protected function configure();

    /**
     * Sets the default extension.
     * Useful when letting Snappy deal with file creation
     *
     * @param string $defaultExtension
     */
    public function setDefaultExtension($defaultExtension)
    {
        $this->defaultExtension = $defaultExtension;
    }

    /**
     * Gets the default extension
     *
     * @return $string
     */
    public function getDefaultExtension()
    {
        return $this->defaultExtension;
    }

    /**
     * Sets an option. Be aware that option values are NOT validated and that
     * it is your responsibility to validate user inputs
     *
     * @param  string $name  The option to set
     * @param  mixed  $value The value (NULL to unset)
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option \'%s\' does not exist.', $name));
        }

        $this->options[$name] = $value;
    }

    /**
     * Sets an array of options
     *
     * @param  array $options An associative array of options as name/value
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Returns all the options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($input, $output, array $options = array(), $overwrite = false)
    {
        if (null === $this->binary) {
            throw new \LogicException(
                'You must define a binary prior to conversion.'
            );
        }

        $this->prepareOutput($output, $overwrite);

        $command = $this->getCommand($input, $output, $options);

        $this->executeCommand($command);

        $this->checkOutput($output, $command);
    }

    /**
     * {@inheritDoc}
     */
    public function generateFromHtml($html, $output, array $options = array(), $overwrite = false)
    {
        $filename = $this->createTemporaryFile($html, 'html');

        $result = $this->generate($filename, $output, $options, $overwrite);

        $this->unlink($filename);
    }

    /**
     * {@inheritDoc}
     */
    public function getOutput($input, array $options = array())
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $filename, $options);

        $result = $this->getFileContents($filename);

        $this->unlink($filename);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputFromHtml($html, array $options = array())
    {
        $filename = $this->createTemporaryFile($html, 'html');

        $result = $this->getOutput($filename, $options);

        $this->unlink($filename);

        return $result;
    }

    /**
     * Defines the binary
     *
     * @param  string $binary The path/name of the binary
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;
    }

    /**
     * Returns the binary
     *
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * Returns the command for the given input and output files
     *
     * @param  string $input   The input file
     * @param  string $output  The ouput file
     * @param  array  $options An optional array of options that will be used
     *                         only for this command
     *
     * @return string
     */
    public function getCommand($input, $output, array $options = array())
    {
        $options = $this->mergeOptions($options);

        return $this->buildCommand($this->binary, $input, $output, $options);
    }

    /**
     * Adds an option
     *
     * @param  string $name    The name
     * @param  mixed  $default An optional default value
     */
    protected function addOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option \'%s\' already exists.', $name));
        }

        $this->options[$name] = $default;
    }

    /**
     * Adds an array of options
     *
     * @param  array $options
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
     * @param  array $options
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
     * Checks the specified output
     *
     * @param  string $output  The output filename
     * @param  string $command The generation command
     *
     * @throws RuntimeException if the output file generation failed
     */
    protected function checkOutput($output, $command)
    {
        // the output file must exist
        if (!$this->fileExists($output)) {
            throw new \RuntimeException(sprintf(
                'The file \'%s\' was not created (command: %s).',
                $output, $command
            ));
        }

        // the output file must not be empty
        if (0 === $this->filesize($output)) {
            throw new \RuntimeException(sprintf(
                'The file \'%s\' was created but is empty (command: %s).',
                $output, $command
            ));
        }
    }

    /**
     * Creates a temporary file.
     * The file is not created if the $content argument is null
     *
     * @param  string $content  Optional content for the temporary file
     * @param  string $extension An optional extension for the filename
     *
     * @return string The filename
     */
    protected function createTemporaryFile($content = null, $extension = null)
    {
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('knp_snappy');

        if (null !== $extension) {
            $filename .= '.'.$extension;
        }

        if (null !== $content) {
            file_put_contents($filename, $content);
        }

        return $filename;
    }

    /**
     * Builds the command string
     *
     * @param  string $binary   The binary path/name
     * @param  string $input    Url or file location of the page to process
     * @param  string $output   File location to the image-to-be
     * @param  array  $options  An array of options
     *
     * @return string
     */
    protected function buildCommand($binary, $input, $output, array $options = array())
    {
        $command = $binary;

        foreach ($options as $key => $value) {
            if (null !== $value && false !== $value) {
                if (true === $value) {
                    $command .= " --".$key;
                } elseif (is_array($value)) {
                    foreach ($value as $v) {
                        $command .= " --".$key." ".escapeshellarg($v);
                    }
                } else {
                    $command .= " --".$key." ".escapeshellarg($value);
                }
            }
        }

        $command .= " \"$input\" \"$output\"";

        return $command;
    }

    /**
     * Executes the given command via shell and returns the complete output as
     * a string
     *
     * @param  string $command
     *
     * @return string
     */
    protected function executeCommand($command)
    {
        return shell_exec($command);
    }

    /**
     * Prepares the specified output
     *
     * @param  string  $filename  The output filename
     * @param  boolean $overwrite Whether to overwrite the file if it already
     *                            exist
     */
    protected function prepareOutput($filename, $overwrite)
    {
        $directory = dirname($filename);

        if ($this->fileExists($filename)) {
            if (!$this->isFile($filename)) {
                throw new \InvalidArgumentException(sprintf(
                    'The output file \'%s\' already exists and it is a %s.',
                    $filename, $this->isDir($filename) ? 'directory' : 'link'
                ));
            } elseif (false === $overwrite) {
                throw new \InvalidArgumentException(sprintf(
                    'The output file \'%s\' already exists.',
                    $filename
                ));
            } elseif (!$this->unlink($filename)) {
                throw new \RuntimeException(sprintf(
                    'Could not delete already existing output file \'%s\'.',
                    $filename
                ));
            }
        } elseif (!$this->isDir($directory) && !$this->mkdir($directory)) {
            throw new \RuntimeException(sprintf(
                'The output file\'s directory \'%s\' could not be created.',
                $directory
            ));
        }
    }

    /**
     * Wrapper for the "file_get_contents" function
     *
     * @param  string $filename
     *
     * @return string
     */
    protected function getFileContents($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Wrapper for the "file_exists" function
     *
     * @param  string $filename
     *
     * @return boolean
     */
    protected function fileExists($filename)
    {
        return file_exists($filename);
    }

    /**
     * Wrapper for the "is_file" method
     *
     * @param  string $filename
     *
     * @return boolean
     */
    protected function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Wrapper for the "filesize" function
     *
     * @param  string $filename
     *
     * @return integer or FALSE on failure
     */
    protected function filesize($filename)
    {
        return filesize($filename);
    }

    /**
     * Wrapper for the "unlink" function
     *
     * @param  string $filename
     *
     * @return boolean
     */
    protected function unlink($filename)
    {
        return unlink($filename);
    }

    /**
     * Wrapper for the "is_dir" function
     *
     * @param  string $filename
     *
     * @return boolean
     */
    protected function isDir($filename)
    {
        return is_dir($filename);
    }

    /**
     * Wrapper for the mkdir function
     *
     * @param  string $pathname
     *
     * @return boolean
     */
    protected function mkdir($pathname)
    {
        return mkdir($pathname, 0777, true);
    }
}
