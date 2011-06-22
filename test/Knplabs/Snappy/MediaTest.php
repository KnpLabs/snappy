<?php

namespace Knplabs\Snappy;

class MediaTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider dataForBuildCommand
	 */
	public function testBuildCommand($binary, $url, $path, $options, $expected)
	{
		$media = $this->getMockForAbstractClass('Knplabs\Snappy\Media', array(), '', false);

		$r = new \ReflectionMethod($media, 'buildCommand');
		$r->setAccessible(true);

		$this->assertEquals($expected, $r->invokeArgs($media, array($binary, $url, $path, $options)));
	}

	public function dataForBuildCommand()
	{
		return array(
			array(
				'thebinary',
				'http://the.url/',
				'/the/path',
				array(),
				'thebinary "http://the.url/" "/the/path"'
			),
			array(
				'thebinary',
				'http://the.url/',
				'/the/path',
				array(
					'foo'	=> null,
					'bar'	=> false,
					'baz'	=> array()
				),
				'thebinary "http://the.url/" "/the/path"'
			),
			array(
				'thebinary',
				'http://the.url/',
				'/the/path',
				array(
					'foo'	=> 'foovalue',
					'bar'	=> array('barvalue1', 'barvalue2'),
					'baz'	=> true
				),
				'thebinary --foo foovalue --bar barvalue1 --bar barvalue2 --baz "http://the.url/" "/the/path"'
			),
		);
	}
}
