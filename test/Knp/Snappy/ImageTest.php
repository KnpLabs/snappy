<?php

namespace Knp\Snappy;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Image();
        $this->assertInstanceOf('\Knp\Snappy\Image', $testObject);
    }
}