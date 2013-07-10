<?php

namespace Knp\Snappy;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Pdf();
        $this->assertInstanceOf('\Knp\Snappy\Pdf', $testObject);
    }
}
