<?php
/**
* test class for Image.
* Abused as testing class for media as well (testing images is easier as pdf's ;)
*/

class ImageTest extends PHPUnit_Framework_TestCase
{

	protected $_imageBinaryLocation = null;
	
	public function __construct()
	{
		$this->_locateBinary();
	}
	
	private function _locateBinary()
	{
		// common locations (gnu/*nix)
		$Locations = array (
						'/usr/local/bin/wkhtmltoimage',
						'/bin/wkhtmltoimage',
						);
		
		foreach ($Locations as $binary) {
			$fileObject = new \SplFileInfo($binary);
    	
    		if ($fileObject->isExecutable()) {
    			$this->_imageBinaryLocation = $binary;
    			continue;
    		}
		}
	
	
	}

	public function testParentIsAbstract()
	{
	    $rClass = new ReflectionClass('\Knplabs\Snappy\Image');
    	$this->assertTrue($rClass->getParentClass()->isAbstract());
	}


	public function testCreateInstance()
	{
		$testObject = new \Knplabs\Snappy\Image();
		$this->assertInstanceOf('\Knplabs\Snappy\Image', $testObject);
	}
	
	/**
     * @expectedException InvalidArgumentException
     */
	public function testSetInvalidExecutable()
	{
		$testObject = new \Knplabs\Snappy\Image('/dev/null/');
		
	}
	
	public function testSetValidExecutable()
	{
		$testObject = new \Knplabs\Snappy\Image();
		$this->assertTrue($testObject->setExecutable($this->_imageBinaryLocation));
	}
	
	
	
	public function testCreateInstanceWithConstantSet()
	{
		if (!defined('SNAPPY_IMAGE_BINARY')) {
			define('SNAPPY_IMAGE_BINARY', $this->_imageBinaryLocation);
		}
		
		$testObject = new \Knplabs\Snappy\Image();
		$this->assertTrue($testObject->executable === $this->_imageBinaryLocation);
	}
}