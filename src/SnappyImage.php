<?php

/**
 * Use this class to create a snapshot / a thumbnail from a url
 *
 * @package Snappy
 * @author Matthieu Bontemps<matthieu.bontemps@knplabs.com>
 */
class SnappyImage
{
    protected $executable;
    
    protected $options = array(
        'allow' => null,                          // Allow the file or files from the specified folder to be loaded (repeatable)
        'background' => null,                     // Do print background (default)
        'no-background' => null,                  // Do not print background
        'cookie' => array(),                      // Set an additional cookie (repeatable)
        'cookie-jar' => null,                     // Read and write cookies from and to the supplied cookie jar file
        'crop-h' => null,                         // Set height for croping
        'crop-w' => null,                         // Set width for croping
        'crop-x' => null,                         // Set x coordinate for croping (default 0)
        'crop-y' => null,                         // Set y coordinate for croping (default 0)
        'custom-header' => array(),               // Set an additional HTTP header (repeatable)
        'custom-header-propagation' => null,      // Add HTTP headers specified by --custom-header for each resource request.
        'no-custom-header-propagation' => null,   // Do not add HTTP headers specified by --custom-header for each resource request.
        'debug-javascript' => null,               // Show javascript debugging output
        'no-debug-javascript' => null,            // Do not show javascript debugging output (default)
        'encoding' => null,                       // Set the default text encoding, for input
        'f' => null,                              // Output format
        'images' => null,                         // Do load or print images (default)
        'no-images' => null,                      // Do not load or print images 
        'disable-javascript' => null,             // Do not allow web pages to run javascript
        'enable-javascript' => null,              // Do allow web pages to run javascript (default)
        'javascript-delay' => null,               // Wait some milliseconds for javascript finish (default 200)
        'load-error-handling' => null,            // Specify how to handle pages that fail to load: abort, ignore or skip (default abort)
        'disable-local-file-access' => null,      // Do not allowed conversion of a local file to read in other local files, unless explecitily allowed with allow
        'enable-local-file-access' => null,       // Allowed conversion of a local file to read in other local files. (default)
        'minimum-font-size' => null,              // Minimum font size
        'password' => null,                       // HTTP Authentication password
        'disable-plugins' => null,                // Disable installed plugins (default)
        'enable-plugins' => null,                 // Enable installed plugins (plugins will likely not work)
        'post' => array(),                        // Add an additional post field
        'post-file' => array(),                   // Post an additional file
        'print-media-type' => null,               // Use print media-type instead of screen
        'no-print-media-type' => null,            // Do not use print media-type instead of screen (default)
        'proxy' => null,                          // Use a proxy
        'readme' => null,                         // Output program readme
        'scale-h' => null,                        // Set height for resizing
        'scale-w' => null,                        // Set width for resizing
        'disable-smart-shrinking' => null,        // Disable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio none constant
        'enable-smart-shrinking' => null,         // Enable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio none constant (default)
        'stop-slow-scripts' => null,              // Stop slow running javascripts
        'no-stop-slow-scripts' => null,           // Do not stop slow running javascripts (default)
        'transparent' => null,                    // Make the background transparrent in pngs *
        'use-xserver' => null,                    // Use the X server (some plugins and other stuff might not work without X11)
        'user-style-sheet' => null,               // Specify a user style sheet, to load with every page
        'username' => null,                       // HTTP Authentication username
        'zoom' => null,                           // Use this zoom factor (default 1)
    );
    
    /**
     * Save a url or file location to an image.
     * Will create directories if needed.
     *
     * @param string Url of the page
     * @param string Path of the future image
     * @return boolean True if success
     */
    public function saveImage($url, $path)
    {
        $command = $this->buildImageCommand($url, $path);
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
        if(!in_array($option, $this->options)) {
            throw new Exception("Invalid option $option");
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
    protected function buildImageCommand($url, $path)
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
