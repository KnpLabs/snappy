<?php

namespace Knp\Snappy;

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Image();
        $this->assertInstanceOf('\Knp\Snappy\Image', $testObject);
    }
}
