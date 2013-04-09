<?php

namespace Knp\Snappy;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Image();
        $this->assertInstanceOf('\Knp\Snappy\Image', $testObject);
    }

    public function testAvailableOptions()
    {
        $testObject = new \Knp\Snappy\Image();
        $testObject->setOption('use-xserver', true);
        $testObject->setOption('enable-smart-width', true);
    }
}
