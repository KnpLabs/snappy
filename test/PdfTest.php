<?php
/**
* Test class for pdf
*/

class PdfTest extends PHPUnit_Framework_TestCase
{

	public function testParentIsAbstract()
	{
	    $rClass = new ReflectionClass('\Knplabs\Snappy\Pdf');
    	$this->assertTrue($rClass->getParentClass()->isAbstract());
	}


}