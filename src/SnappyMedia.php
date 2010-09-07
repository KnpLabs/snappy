<?php

/**
* 
*/
abstract class SnappyMedia
{
    protected $executable;
    protected $options = array();
    protected $defaultExtension;
    
    /**
     * Write the media to the standard output.
     *
     * @param string Url of the page
     * @return void
     */
    public function output($url)
    {
      $file = tempnam(sys_get_temp_dir(), 'snappy') . '.' . $this->defaultExtension;

      $ok = $this->save($url, $file);
      readfile($file);
      unlink($file);
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
        $command = $this->buildCommand($url, $path);
        $basePath = dirname($path);
        if(!is_dir($basePath)) {
          mkdir($basePath, 0777, true);
        }
        if(file_exists($path)) {
          unlink($path);
        }
        $ok = $this->exec($command);
        return file_exists($path);
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
            throw new Exception("Invalid option '$option'");
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
