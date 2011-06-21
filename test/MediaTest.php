<?php
/**
* Only one test makes sense...are we an abstract or not.
* The behaviour of the other classes depend on this.
*/

class MediaTest extends PHPUnit_Framework_TestCase
{

	public function testIfIsAbstract()
	{
	    $rClass = new ReflectionClass('\Knplabs\Snappy\Media');
    	$this->assertTrue($rClass->isAbstract());
	}


}