<?php

namespace Knp\Snappy;

class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $this->assertEquals([], $media->getOptions());

        $r = new \ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, ['foo', 'bar']);

        $this->assertEquals(['foo' => 'bar'], $media->getOptions(), '->addOption() adds an option');

        $r->invokeArgs($media, ['baz', 'bat']);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat'
            ],
            $media->getOptions(),
            '->addOption() appends the option to the existing ones'
        );

        $message = '->addOption() raises an exception when the specified option already exists';
        try {
            $r->invokeArgs($media, ['baz', 'bat']);
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testAddOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $this->assertEquals([], $media->getOptions());

        $r = new \ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, [['foo' => 'bar', 'baz' => 'bat']]);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat'
            ],
            $media->getOptions(),
            '->addOptions() adds all the given options'
        );

        $r->invokeArgs($media, [['ban' => 'bag', 'bal' => 'bac']]);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat',
                'ban' => 'bag',
                'bal' => 'bac'
            ],
            $media->getOptions(),
            '->addOptions() adds the given options to the existing ones'
        );

        $message = '->addOptions() raises an exception when one of the given options already exists';
        try {
            $r->invokeArgs($media, [['bak' => 'bam', 'bah' => 'bap', 'baz' => 'bat']]);
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testSetOption()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $r = new \ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, ['foo', 'bar']);

        $media->setOption('foo', 'abc');

        $this->assertEquals(
            [
                'foo' => 'abc'
            ],
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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $r = new \ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, [['foo' => 'bar', 'baz' => 'bat']]);

        $media->setOptions(['foo' => 'abc', 'baz' => 'def']);

        $this->assertEquals(
            [
                'foo'   => 'abc',
                'baz'   => 'def'
            ],
            $media->getOptions(),
            '->setOptions() defines the values of all the specified options'
        );

        $message = '->setOptions() raises an exception when one of the specified options does not exist';
        try {
            $media->setOptions(['foo' => 'abc', 'baz' => 'def', 'bad' => 'ghi']);
            $this->fail($message);
        } catch (\InvalidArgumentException $e) {
            $this->anything($message);
        }
    }

    public function testGenerate()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'prepareOutput',
                'getCommand',
                'executeCommand',
                'checkOutput',
                'checkProcessStatus',
            ],
            [
                'the_binary',
                []
            ]
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
                $this->equalTo(['foo' => 'bar'])
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

        $media->generate('the_input_file', 'the_output_file', ['foo' => 'bar']);
    }

    public function testGenerateFromHtml()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'generate',
                'createTemporaryFile',
            ],
            [
                'the_binary'
            ],
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
            ->method('generate')
            ->with(
                $this->equalTo(['the_temporary_file']),
                $this->equalTo('the_output_file'),
                $this->equalTo(['foo' => 'bar'])
            )
        ;

        $media->generateFromHtml('<html>foo</html>', 'the_output_file', ['foo' => 'bar']);
    }

    public function testGenerateFromHtmlWithHtmlArray()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'generate',
                'createTemporaryFile',
            ],
            [
                'the_binary'
            ],
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
            ->method('generate')
            ->with(
                $this->equalTo(['the_temporary_file']),
                $this->equalTo('the_output_file'),
                $this->equalTo(['foo' => 'bar'])
            )
        ;

        $media->generateFromHtml(['<html>foo</html>'], 'the_output_file', ['foo' => 'bar']);
    }

    public function testGetOutput()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'getDefaultExtension',
                'createTemporaryFile',
                'generate',
                'getFileContents',
                'unlink'
            ],
            [],
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
                $this->equalTo(['foo' => 'bar'])
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

        $this->assertEquals('the file contents', $media->getOutput('the_input_file', ['foo' => 'bar']));
    }

    public function testGetOutputFromHtml()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'getOutput',
                'createTemporaryFile',
            ],
            [],
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
                $this->equalTo(['the_temporary_file']),
                $this->equalTo(['foo' => 'bar'])
            )
            ->will($this->returnValue('the output'))
        ;

        $this->assertEquals('the output', $media->getOutputFromHtml('<html>foo</html>', ['foo' => 'bar']));
    }

    public function testGetOutputFromHtmlWithHtmlArray()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'getOutput',
                'createTemporaryFile',
            ],
            [],
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
                $this->equalTo(['the_temporary_file']),
                $this->equalTo(['foo' => 'bar'])
            )
            ->will($this->returnValue('the output'))
        ;

        $this->assertEquals('the output', $media->getOutputFromHtml(['<html>foo</html>'], ['foo' => 'bar']));
    }

    public function testMergeOptions()
    {
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $originalOptions = ['foo' => 'bar', 'baz' => 'bat'];

        $addOptions = new \ReflectionMethod($media, 'addOptions');
        $addOptions->setAccessible(true);
        $addOptions->invokeArgs($media, [$originalOptions]);

        $r = new \ReflectionMethod($media, 'mergeOptions');
        $r->setAccessible(true);

        $mergedOptions = $r->invokeArgs($media, [['foo' => 'ban']]);

        $this->assertEquals(
            [
                'foo' => 'ban',
                'baz' => 'bat'
            ],
            $mergedOptions,
            '->mergeOptions() merges an option to the instance ones and returns the result options array'
        );

        $this->assertEquals(
            $originalOptions,
            $media->getOptions(),
            '->mergeOptions() does NOT change the instance options'
        );

        $mergedOptions = $r->invokeArgs($media, [['foo' => 'ban', 'baz' => 'bag']]);

        $this->assertEquals(
            [
                'foo' => 'ban',
                'baz' => 'bag'
            ],
            $mergedOptions,
            '->mergeOptions() merges many options to the instance ones and returns the result options array'
        );

        $message = '->mergeOptions() throws an InvalidArgumentException once there is an undefined option in the given array';
        try {
            $r->invokeArgs($media, [['foo' => 'ban', 'bad' => 'bah']]);
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
        $media = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $r = new \ReflectionMethod($media, 'buildCommand');
        $r->setAccessible(true);

        $this->assertEquals($expected, $r->invokeArgs($media, [$binary, $url, $path, $options]));
    }

    private function getPHPExecutableFromPath() {
        if (isset($_SERVER["_"])) {
            return $_SERVER["_"];
        }

        if (@defined(PHP_BINARY)) {
            return PHP_BINARY;
        }

        $paths = explode(PATH_SEPARATOR, getenv('PATH'));
        foreach ($paths as $path) {
            // we need this for XAMPP (Windows)
            if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
                return $path;
            }
            else {
                $php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
                if (file_exists($php_executable) && is_file($php_executable)) {
                    return $php_executable;
                }
            }
        }
        return FALSE; // not found
    }

    public function dataForBuildCommand()
    {
        $theBinary = $this->getPHPExecutableFromPath() . ' -v'; // i.e.: '/usr/bin/php -v'

        return [
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [],
                $theBinary . ' ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'foo'   => null,
                    'bar'   => false,
                    'baz'   => []
                ],
                $theBinary . ' ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'foo'   => 'foovalue',
                    'bar'   => ['barvalue1', 'barvalue2'],
                    'baz'   => true
                ],
                $theBinary . ' --foo ' . escapeshellarg('foovalue') . ' --bar ' . escapeshellarg('barvalue1') . ' --bar ' . escapeshellarg('barvalue2') . ' --baz ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'cookie'   => ['session' => 'bla', 'phpsess' => 12],
                    'no-background'   => '1',
                ],
                $theBinary . ' --cookie ' . escapeshellarg('session') . ' ' . escapeshellarg('bla') . ' --cookie ' .  escapeshellarg('phpsess') . ' ' . escapeshellarg('12') . ' --no-background ' . escapeshellarg('1') . ' ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'allow'   => ['/path1', '/path2'],
                    'no-background'   => '1',
                ],
                $theBinary . ' --allow ' . escapeshellarg('/path1') . ' --allow ' . escapeshellarg('/path2') . ' --no-background ' . escapeshellarg('1') . ' ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'image-dpi' => 100,
                    'image-quality' => 50,
                ],
                $theBinary . ' ' . '--image-dpi 100 --image-quality 50 ' . escapeshellarg('http://the.url/') . ' ' . escapeshellarg('/the/path')
            ],
        ];
    }

    public function testCheckOutput()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'fileExists',
                'filesize'
            ],
            [],
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
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->anything($message);
        } catch (\RuntimeException $e) {
            $this->fail($message);
        }
    }

    public function testCheckOutputWhenTheFileDoesNotExist()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'fileExists',
                'filesize'
            ],
            [],
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
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->fail($message);
        } catch (\RuntimeException $e) {
            $this->anything($message);
        }
    }

    public function testCheckOutputWhenTheFileIsEmpty()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'fileExists',
                'filesize'
            ],
            [],
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
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->fail($message);
        } catch (\RuntimeException $e) {
            $this->anything($message);
        }
    }

    public function testCheckProcessStatus()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'isIgnoreContentNotFound'
            ],
            [],
            '',
            false
        );

        $r = new \ReflectionMethod($media, 'checkProcessStatus');
        $r->setAccessible(true);

        try {
            $r->invokeArgs($media, [0, '', '', 'the command']);
            $this->anything('0 status means success');
        } catch (\RuntimeException $e) {
            $this->fail('0 status means success');
        }

        try {
            $r->invokeArgs($media, [1, '', '', 'the command']);
            $this->anything('1 status means failure, but no stderr content');
        } catch (\RuntimeException $e) {
            $this->fail('1 status means failure, but no stderr content');
        }

        try {
            $r->invokeArgs($media, [1, '', 'Could not connect to X', 'the command']);
            $this->fail('1 status means failure');
        } catch (\RuntimeException $e) {
            $this->anything('1 status means failure');
        }

        $media->expects($this->once())
            ->method('isIgnoreContentNotFound')
            ->will($this->returnValue(true))
        ;
        try {
            $r->invokeArgs($media, [1, '', 'ContentNotFound', 'the command']);
            $this->anything('1 with option ignorecontentnotfound means success');
        } catch (\RuntimeException $e) {
            $this->fail('1 status means failure, but ignorecontentnotfound is set');
        }
    }

    /**
     * @dataProvider dataForIsAssociativeArray
     */
    public function testIsAssociativeArray($array, $isAssociativeArray)
    {
        $generator = $this->getMockForAbstractClass('Knp\Snappy\AbstractGenerator', [], '', false);

        $r = new \ReflectionMethod($generator, 'isAssociativeArray');
        $r->setAccessible(true);
        $this->assertEquals($isAssociativeArray, $r->invokeArgs($generator, [$array]));
    }

    /**
     * @expectedException Knp\Snappy\Exception\FileAlreadyExistsException
     */
    public function testItThrowsTheProperExceptionWhenFileExistsAndNotOverwritting()
    {
        $media = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'fileExists',
                'isFile'
            ],
            [],
            '',
            false
        );
        $media
            ->expects($this->any())
            ->method('fileExists')
            ->will($this->returnValue(true))
        ;
        $media
            ->expects($this->any())
            ->method('isFile')
            ->will($this->returnValue(true))
        ;
        $r = new \ReflectionMethod($media, 'prepareOutput');
        $r->setAccessible(true);

        $r->invokeArgs($media, ['', false]);
    }

    public function dataForIsAssociativeArray()
    {
        return [
            [
                ['key' => 'value'],
                true
            ],
            [
                ['key' => 2],
                true
            ],
            [
                ['key' => 'value', 'key2' => 'value2'],
                true
            ],
            [
                [0 => 'value', 1 => 'value2', 'deux' => 'value3'],
                true
            ],
            [
                [0 => 'value'],
                false
            ],
            [
                [0 => 'value', 1 => 'value2', 3 => 'value3'],
                false
            ],
            [
                ['0' => 'value', '1' => 'value2', '3' => 'value3'],
                false
            ],
            [
                [],
                false
            ],
        ];
    }

    public function testCleanupEmptyTemporaryFiles()
    {
        $generator = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'unlink',
            ],
            [
                'the_binary'
            ]
        );
        $generator
            ->expects($this->once())
            ->method('unlink');

        $create = new \ReflectionMethod($generator, 'createTemporaryFile');
        $create->setAccessible(true);
        $create->invoke($generator, null, null);

        $files = new \ReflectionProperty($generator, 'temporaryFiles');
        $files->setAccessible(true);
        $this->assertCount(1, $files->getValue($generator));

        $remove = new \ReflectionMethod($generator, 'removeTemporaryFiles');
        $remove->setAccessible(true);
        $remove->invoke($generator);
    }

    public function testleanupTemporaryFiles()
    {
        $generator = $this->getMock(
            'Knp\Snappy\AbstractGenerator',
            [
                'configure',
                'unlink',
            ],
            [
                'the_binary'
            ]
        );
        $generator
            ->expects($this->once())
            ->method('unlink');

        $create = new \ReflectionMethod($generator, 'createTemporaryFile');
        $create->setAccessible(true);
        $create->invoke($generator, '<html/>', 'html');

        $files = new \ReflectionProperty($generator, 'temporaryFiles');
        $files->setAccessible(true);
        $this->assertCount(1, $files->getValue($generator));

        $remove = new \ReflectionMethod($generator, 'removeTemporaryFiles');
        $remove->setAccessible(true);
        $remove->invoke($generator);
    }
}
