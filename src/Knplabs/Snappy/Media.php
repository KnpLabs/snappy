<?php

namespace Knplabs\Snappy;

/**
 * Base class for Snappy Media
 */
abstract class Media
{
    protected $binary;
    protected $defaultExtension;

    const URL_PATTERN = '~^
            (http|https|ftp)://                       # protocol
            (
                ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
                    |                                 #  or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
            )
            (:[0-9]+)?                                # a port (optional)
            (/?|/\S+)                                 # a /, nothing or a / with something
        $~ix';

    /**
	 * Constructor
     *
     * @param  string $binary
     * @param  array  $options
     */
    public function __construct($binary, array $options)
    {
        if (!$this->isExecAllowed()) {
			throw new \RuntimeException('The \'shell_exec\' function is not allowed on this php install.');
        }

        if (null !== $binary) {
            $this->setBinary($binary);
        }

        if (count($options) !== 0) {
            $this->mergeOptions($options);
        }
    }

    /**
	 * Sets an option. Be aware that option values are NOT validated and that
	 * it is your responsibility to validate user inputs
     *
     * @param  string 		$option The option to set
     * @param  string|array $value  The value for the option (NULL to unset)
	 *
     * @return void
     */
    public function setOption($option, $value = null)
    {
        if (!array_key_exists($option, $this->options)) {
            throw new \Exception("Invalid option ".$option);
        }

        $this->options[$option] = $value;
    }

    /**
     * Writes the media to the standard output
     *
     * @param  string $url Url of the page
	 *
     * @return void
     */
    public function output($url)
    {
        $file = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.' . $this->defaultExtension;

        $ok = $this->save($url, $file);

        readfile($file);
        unlink($file);
    }

    /**
     * Returns the content of a media
     *
     * @param  string $url Url of the page
	 *
     * @return string
     */
    public function get($url)
    {
        $file = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.' . $this->defaultExtension;

        $ok = $this->save($url, $file);
        $content = null;
        $content = file_get_contents($file);

        return $content;
    }

    /**
	 * Creates the media from the specified url and saves it in the specified
	 * path. It will create the directory if needed
     *
     * @param  string $url	Url of the page
     * @param  string $path Path of the future image
	 *
     * @return boolean TRUE on success, or FALSE on failure
     */
    public function save($url, $path)
    {
        if ($this->binary === null) {
            throw new \exception("Binary not set");
        }

        if (!preg_match(self::URL_PATTERN, $url)) {
            $data = $url;
            $url = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.html';
            file_put_contents($url, $data);
        }

        $command = $this->buildCommand($url, $path, $this->options);
        $basePath = dirname($path);

        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        if (file_exists($path)) {
            unlink($path);
        }

        $ok = $this->exec($command);

        return file_exists($path) && filesize($path);
    }

    /**
	 * Defines the location of the binary and validates it
     *
     * @param  string $binary The path/name of the binary
     */
    public function setBinary($binary)
    {
        if (!$this->validateBinary($binary)) {
            throw new \InvalidArgumentException(sprintf('The binary \'%s\' does not exist or is not binary.', $binary));
        }

        $this->binary = $binary;
    }

    /**
     * Indicates whether the "check_exec" function is allowed
     *
     * @return boolean
     */
    private function isExecAllowed()
    {
        $disabled = explode(', ', ini_get('disable_functions'));

        return (bool) !in_array('shell_exec', $disabled);
    }

    /**
	 * Tests the requested binary against an array with known/allowed
	 * binaries for this class and if the binary exists and is binary
     *
     * @param  string $binary The path/name of the binary
	 *
     * @return boolean
     */
    private function validateBinary($binary)
    {
        $knownBinaries = array(
            'wkhtmltoimage',
            'wkhtmltopdf',
        );
        $fileObject = new \SplFileInfo($binary);

        return $fileObject->isBinary() && in_array($fileObject->getBasename(), $knownBinaries);
    }

    /**
	 * Merges the given options array with the current options
     *
     * @param  array $options
	 *
     * @return void
     */
    private function mergeOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    /**
     * Returns the command to wkhtmltoimage using the options attributes
     *
	 * @param  string $binary	The binary path/name
     * @param  string $url     	Url or file location of the page to process
     * @param  string $path    	File location to the image-to-be
	 * @param  array  $options 	An array of options
	 *
     * @return string The command
     */
    protected function buildCommand($binary, $url, $path, array $options)
    {
        $command = $binary;

        foreach ($options as $key => $value) {
            if (null !== $value && false !== $value) {
                if (true === $value) {
                    $command .= " --".$key;
                } elseif (is_array($value)) {
                    foreach ($value as $v) {
                        $command .= " --".$key." ".$v;
                    }
                } else {
                    $command .= " --".$key." ".$value;
                }
            }
        }

        $command .= " \"$url\" \"$path\"";

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
    protected function exec($command)
    {
        return shell_exec($command);
    }
}
