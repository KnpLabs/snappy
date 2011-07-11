<?php

namespace Knp\Snappy;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

        $this->assertEquals(array(), $media->getOptions());

        $r = new \ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, array('foo', 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $media->getOptions(), '->addOption() adds an option');

        $r->invokeArgs($media, array('baz', 'bat'));

        $this->assertEquals(
            array(
                'foo' => 'bar',
                'baz' => 'bat'
            ),
            $media->getOptions(),
            '->addOption() appends the option to the existing ones'
        );

        $message = '->addOption() raises an exception when the specified option already exists';
        try {
            $r->invokeArgs($media, array('baz', 'bat'));
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testAddOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

        $this->assertEquals(array(), $media->getOptions());

        $r = new \ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, array(array('foo' => 'bar', 'baz' => 'bat')));

        $this->assertEquals(
            array(
                'foo' => 'bar',
                'baz' => 'bat'
            ),
            $media->getOptions(),
            '->addOptions() adds all the given options'
        );

        $r->invokeArgs($media, array(array('ban' => 'bag', 'bal' => 'bac')));

        $this->assertEquals(
            array(
                'foo' => 'bar',
                'baz' => 'bat',
                'ban' => 'bag',
                'bal' => 'bac'
            ),
            $media->getOptions(),
            '->addOptions() adds the given options to the existing ones'
        );

        $message = '->addOptions() raises an exception when one of the given options already exists';
        try {
            $r->invokeArgs($media, array(array('bak' => 'bam', 'bah' => 'bap', 'baz' => 'bat')));
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testSetOption()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

        $r = new \ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, array('foo', 'bar'));

        $media->setOption('foo', 'abc');

        $this->assertEquals(
            array(
                'foo' => 'abc'
            ),
            $media->getOptions(),
            '->setOption() defines the value of an option'
        );

        $message = '->setOption() raises an exception when the specified option does not exist';
        try {
            $media->setOption('bad', 'def');
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testSetOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

        $r = new \ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, array(array('foo' => 'bar', 'baz' => 'bat')));

        $media->setOptions(array('foo' => 'abc', 'baz' => 'def'));

        $this->assertEquals(
            array(
                'foo'   => 'abc',
                'baz'   => 'def'
            ),
            $media->getOptions(),
            '->setOptions() defines the values of all the specified options'
        );

        $message = '->setOptions() raises an exception when one of the specified options does not exist';
        try {
            $media->setOptions(array('foo' => 'abc', 'baz' => 'def', 'bad' => 'ghi'));
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testMergeOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

        $originalOptions = array('foo' => 'bar', 'baz' => 'bat');

        $addOptions = new \ReflectionMethod($media, 'addOptions');
        $addOptions->setAccessible(true);
        $addOptions->invokeArgs($media, array($originalOptions));

        $r = new \ReflectionMethod($media, 'mergeOptions');
        $r->setAccessible(true);

        $mergedOptions = $r->invokeArgs($media, array(array('foo' => 'ban')));

        $this->assertEquals(
            array(
                'foo' => 'ban',
                'baz' => 'bat'
            ),
            $mergedOptions,
            '->mergeOptions() merges an option to the instance ones and returns the result options array'
        );

        $this->assertEquals(
            $originalOptions,
            $media->getOptions(),
            '->mergeOptions() does NOT change the instance options'
        );

        $mergedOptions = $r->invokeArgs($media, array(array('foo' => 'ban', 'baz' => 'bag')));

        $this->assertEquals(
            array(
                'foo' => 'ban',
                'baz' => 'bag'
            ),
            $mergedOptions,
            '->mergeOptions() merges many options to the instance ones and returns the result options array'
        );

        $message = '->mergeOptions() throws an InvalidArgumentException once there is an undefined option in the given array';
        try {
            $r->invokeArgs($media, array(array('foo' => 'ban', 'bad' => 'bah')));
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    /**
     * @dataProvider dataForBuildCommand
     */
    public function testBuildCommand($binary, $url, $path, $options, $expected)
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\Media', array(), '', false);

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
                    'foo'   => null,
                    'bar'   => false,
                    'baz'   => array()
                ),
                'thebinary "http://the.url/" "/the/path"'
            ),
            array(
                'thebinary',
                'http://the.url/',
                '/the/path',
                array(
                    'foo'   => 'foovalue',
                    'bar'   => array('barvalue1', 'barvalue2'),
                    'baz'   => true
                ),
                'thebinary --foo \'foovalue\' --bar \'barvalue1\' --bar \'barvalue2\' --baz "http://the.url/" "/the/path"'
            ),
        );
    }
}
