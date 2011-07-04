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
        $locations = array (
                        '/usr/local/bin/wkhtmltoimage',
                        '/bin/wkhtmltoimage',
                        );

        foreach ($locations as $binary) {
            $fileObject = new \SplFileInfo($binary);

            if ($fileObject->isExecutable()) {
                $this->_imageBinaryLocation = $binary;
                continue;
            }
        }


    }

    public function testParentIsAbstract()
    {
        $rClass = new ReflectionClass('\Knp\Snappy\Image');
        $this->assertTrue($rClass->getParentClass()->isAbstract());
    }


    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Image();
        $this->assertInstanceOf('\Knp\Snappy\Image', $testObject);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidExecutable()
    {
        $testObject = new \Knp\Snappy\Image('/dev/null/');

    }

    public function testSetValidExecutable()
    {
        $testObject = new \Knp\Snappy\Image();
        $this->assertTrue($testObject->setExecutable($this->_imageBinaryLocation));
    }



    public function testCreateInstanceWithConstantSet()
    {
        if (!defined('SNAPPY_IMAGE_BINARY')) {
            define('SNAPPY_IMAGE_BINARY', $this->_imageBinaryLocation);
        }

        $testObject = new \Knp\Snappy\Image();
        $this->assertTrue($testObject->executable === $this->_imageBinaryLocation);
    }
}
