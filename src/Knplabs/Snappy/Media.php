<?php

namespace Knplabs\Snappy;

/**
* Base class for Snappy Media
*/
abstract class Media
{
    protected $executable;
    protected $options = array();
    protected $defaultExtension;
    protected $extension;

    const URL_PATTERN = '~^
            (http|https|ftp)://                                 # protocol
            (
                ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
                    |                                   #  or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
            )
            (:[0-9]+)?                              # a port (optional)
            (/?|/\S+)                               # a /, nothing or a / with something
        $~ix';
    
    /**
     * Constructor. Set executable path and merge options (passed as an array) with default options.
     *
     * @param string $executable 
     * @param array $options 
     */
    function __construct($executable, array $options = array(), $ext = null)
    {
	if ($ext != null) {
		$this->extension = $ext;
	}
	else {
		$this->extension = $defaultExtension;
	}

        $this->setExecutable($executable);
        $this->mergeOptions($options);
    }
    
    /**
     * Write the media to the standard output.
     *
     * @param string Url of the page
     * @return void
     */
    public function output($url)
    {
        $file = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.' . $this->extension;

        $ok = $this->save($url, $file);
        $content = null;
        readfile($file);
        unlink($file);
    }

    /**
     * Return the content of a media
     *
     * @param string Url of the page
     * @return string
     */
    public function get($url)
    {
        $file = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.' . $this->extension;

        $ok = $this->save($url, $file);
        $content = null;
        $content = file_get_contents($file);
        return $content;
    }
    
    /**
     * Save a url or file location to an image.
     * Will create directories if needed.
     *
     * @param string Url of the page
     * @param string Path of the future image
     * @return boolean True if success
     */
    public function save($url, $path)
    {
        if(!preg_match(self::URL_PATTERN, $url)) {
            $data = $url;
            $url = tempnam(sys_get_temp_dir(), 'knplabs_snappy') . '.html';
            file_put_contents($url, $data);
        }
        $command = $this->buildCommand($url, $path);
        $basePath = dirname($path);
        if(!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }
        if(file_exists($path)) {
            unlink($path);
        }
        $ok = $this->exec($command);
        return file_exists($path) && filesize($path);
    }
    
    public function setExecutable($executable)
    {
        $this->executable = $executable;
    }
    
    /**
     * Set a wkhtmltoimage option. Be aware that option values are NOT validated
     * and that it is your responsibility to validate user inputs.
     * 
     * @param string Option 
     * @param string|array Value. Null to unset the option. 
     * @return void
     */
    public function setOption($option, $value = null)
    {
        if(!array_key_exists($option, $this->options)) {
            throw new \Exception("Invalid option '$option'");
        }
        $this->options[$option] = $value;
    }
    
    /**
     * Merge wkhtmltoimage options (passed as an array) with current options
     *
     * @param array Array of options
     * @return void
     */
    public function mergeOptions(array $options)
    {
        foreach($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }
    
    /**
     * Return the command to wkhtmltoimage using the options attributes
     *
     * @param string Url or file location of the page to process
     * @param string File location to the image-to-be
     * @return string The command
     */
    protected function buildCommand($url, $path)
    {
        $command = $this->executable;

        foreach($this->options as $key => $value) {
            if(null !== $value && false !== $value) {
                if(true === $value) {
                    $command .= " --$key";
                } elseif(is_array($value)) {
                    foreach($value as $v) {
                        $command .= " --$key $v";
                    }
                } else {
                    $command .= " --$key $value";
                }
            }
        }
        
        $command .= " \"$url\" \"$path\"";
        return $command;
    }
        
    protected function exec($command)
    {
        return shell_exec($command);
    }
}
