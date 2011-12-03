<?php

namespace Knp\Snappy;

class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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

    public function testGenerate()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'prepareOutput',
                'getCommand',
                'executeCommand',
                'checkOutput',
                'checkProcessStatus',
            ),
            array(
                'the_binary',
                array()
            )
        );
        $media
            ->expects($this->once())
            ->method('prepareOutput')
            ->with($this->equalTo('the_output_file'))
        ;
        $media
            ->expects($this->any())
            ->method('getCommand')
            ->with(
                $this->equalTo('the_input_file'),
                $this->equalTo('the_output_file'),
                $this->equalTo(array('foo' => 'bar'))
            )
            ->will($this->returnValue('the command'))
        ;
        $media
            ->expects($this->once())
            ->method('executeCommand')
            ->with($this->equalTo('the command'))
        ;
        $media
            ->expects($this->once())
            ->method('checkProcessStatus')
            ->with(null, '', '', 'the command')
        ;
        $media
            ->expects($this->once())
            ->method('checkOutput')
            ->with(
                $this->equalTo('the_output_file'),
                $this->equalTo('the command')
            )
        ;

        $media->generate('the_input_file', 'the_output_file', array('foo' => 'bar'));
    }

    public function testGenerateFromHtml()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'generate',
                'createTemporaryFile',
                'unlink'
            ),
            array(
                'the_binary'
            ),
            '',
            false
        );
        $media
            ->expects($this->once())
            ->method('createTemporaryFile')
            ->with(
                $this->equalTo('<html>foo</html>'),
                $this->equalTo('html')
            )
            ->will($this->returnValue('the_temporary_file'))
        ;
        $media
            ->expects($this->once())
            ->method('unlink')
            ->with($this->equalTo('the_temporary_file'))
            ->will($this->returnValue(true))
        ;
        $media
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('the_temporary_file'),
                $this->equalTo('the_output_file'),
                $this->equalTo(array('foo' => 'bar'))
            )
        ;

        $media->generateFromHtml('<html>foo</html>', 'the_output_file', array('foo' => 'bar'));
    }

    public function testGetOutput()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'getDefaultExtension',
                'createTemporaryFile',
                'generate',
                'getFileContents',
                'unlink'
            ),
            array(),
            '',
            false
        );
        $media
            ->expects($this->any())
            ->method('getDefaultExtension')
            ->will($this->returnValue('ext'))
        ;
        $media
            ->expects($this->any())
            ->method('createTemporaryFile')
            ->with(
                $this->equalTo(null),
                $this->equalTo('ext')
            )
            ->will($this->returnValue('the_temporary_file'))
        ;
        $media
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('the_input_file'),
                $this->equalTo('the_temporary_file'),
                $this->equalTo(array('foo' => 'bar'))
            )
        ;
        $media
            ->expects($this->once())
            ->method('getFileContents')
            ->will($this->returnValue('the file contents'))
        ;

        $media
            ->expects($this->any())
            ->method('unlink')
            ->with($this->equalTo('the_temporary_file'))
            ->will($this->returnValue(true))
        ;

        $this->assertEquals('the file contents', $media->getOutput('the_input_file', array('foo' => 'bar')));
    }

    public function testGetOutputFromHtml()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'getOutput',
                'createTemporaryFile',
                'unlink'
            ),
            array(),
            '',
            false
        );
        $media
            ->expects($this->once())
            ->method('createTemporaryFile')
            ->with(
                $this->equalTo('<html>foo</html>'),
                $this->equalTo('html')
            )
            ->will($this->returnValue('the_temporary_file'))
        ;
        $media
            ->expects($this->once())
            ->method('getOutput')
            ->with(
                $this->equalTo('the_temporary_file'),
                $this->equalTo(array('foo' => 'bar'))
            )
            ->will($this->returnValue('the output'))
        ;
        $media
            ->expects($this->once())
            ->method('unlink')
            ->with($this->equalTo('the_temporary_file'))
            ->will($this->returnValue(true))
        ;

        $this->assertEquals('the output', $media->getOutputFromHtml('<html>foo</html>', array('foo' => 'bar')));
    }

    public function testMergeOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

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
                "thebinary 'http://the.url/' '/the/path'"
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
                "thebinary 'http://the.url/' '/the/path'"
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
                "thebinary --foo 'foovalue' --bar 'barvalue1' --bar 'barvalue2' --baz 'http://the.url/' '/the/path'"
            ),
            array(
                'thebinary',
                'http://the.url/',
                '/the/path',
                array(
                    'cookie'   => array('session' => 'bla', 'phpsess' => 12),
                    'no-background'   => '1',
                ),
                "thebinary --cookie 'session' 'bla' --cookie 'phpsess' '12' --no-background '1' 'http://the.url/' '/the/path'"
            ),
            array(
                'thebinary',
                'http://the.url/',
                '/the/path',
                array(
                    'allow'   => array('/path1', '/path2'),
                    'no-background'   => '1',
                ),
                "thebinary --allow '/path1' --allow '/path2' --no-background '1' 'http://the.url/' '/the/path'"
            ),
        );
    }

    public function testCheckOutput()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'fileExists',
                'filesize'
            ),
            array(),
            '',
            false
        );
        $media
            ->expects($this->once())
            ->method('fileExists')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(true))
        ;
        $media
            ->expects($this->once())
            ->method('filesize')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(123))
        ;

        $r = new \ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() checks both file existence and size';
        try {
            $r->invokeArgs($media, array('the_output_file', 'the command'));
            $this->anything($message);
        } catch (\RuntimeException $e) {
            $this->fail($message);
        }
    }

    public function testCheckOutputWhenTheFileDoesNotExist()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'fileExists',
                'filesize'
            ),
            array(),
            '',
            false
        );
        $media
            ->expects($this->once())
            ->method('fileExists')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(false))
        ;

        $r = new \ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() throws an InvalidArgumentException when the file does not exist';
        try {
            $r->invokeArgs($media, array('the_output_file', 'the command'));
            $this->fail($message);
        } catch (\RuntimeException $e) {
            $this->anything($message);
        }
    }

    public function testCheckOutputWhenTheFileIsEmpty()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
                'fileExists',
                'filesize'
            ),
            array(),
            '',
            false
        );
        $media
            ->expects($this->once())
            ->method('fileExists')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(true))
        ;
        $media
            ->expects($this->once())
            ->method('filesize')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(0))
        ;

        $r = new \ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() throws an InvalidArgumentException when the file is empty';
        try {
            $r->invokeArgs($media, array('the_output_file', 'the command'));
            $this->fail($message);
        } catch (\RuntimeException $e) {
            $this->anything($message);
        }
    }

    public function testCheckProcessStatus()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            array(
                'configure',
            ),
            array(),
            '',
            false
        );

        $r = new \ReflectionMethod($media, 'checkProcessStatus');
        $r->setAccessible(true);

        try {
            $r->invokeArgs($media, array(0, '', '', 'the command'));
            $this->anything('0 status means success');
        } catch (\RuntimeException $e) {
            $this->fail('0 status means success');
        }

        try {
            $r->invokeArgs($media, array(1, '', '', 'the command'));
            $this->anything('1 status means failure, but no stderr content');
        } catch (\RuntimeException $e) {
            $this->fail('1 status means failure, but no stderr content');
        }

        try {
            $r->invokeArgs($media, array(1, '', 'Could not connect to X', 'the command'));
            $this->fail('1 status means failure');
        } catch (\RuntimeException $e) {
            $this->anything('1 status means failure');
        }
    }

    /**
     * @dataProvider dataForIsAssociativeArray
     */
    public function testIsAssociativeArray($array, $isAssociativeArray)
    {
        $generator = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', array(), '', false);

        $r = new \ReflectionMethod($generator, 'isAssociativeArray');
        $r->setAccessible(true);
        $this->assertEquals($isAssociativeArray, $r->invokeArgs($generator, array($array)));
    }

    public function dataForIsAssociativeArray()
    {
        return array(
            array(
                array('key' => 'value'),
                true
            ),
            array(
                array('key' => 2),
                true
            ),
            array(
                array('key' => 'value', 'key2' => 'value2'),
                true
            ),
            array(
                array(0 => 'value', 1 => 'value2', 'deux' => 'value3'),
                true
            ),
            array(
                array(0 => 'value'),
                false
            ),
            array(
                array(0 => 'value', 1 => 'value2', 3 => 'value3'),
                false
            ),
            array(
                array('0' => 'value', '1' => 'value2', '3' => 'value3'),
                false
            ),
            array(
                array(),
                false
            ),
        );
    }
}
