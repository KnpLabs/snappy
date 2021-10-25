<?php

namespace Tests\Knp\Snappy;

use Knp\Snappy\AbstractGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use RuntimeException;
use ReflectionProperty;
use ReflectionMethod;

class AbstractGeneratorTest extends TestCase
{
    public function testAddOption(): void
    {
        $media = $this->getMockForAbstractClass(AbstractGenerator::class, [], '', false);

        $this->assertEquals([], $media->getOptions());

        $r = new ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, ['foo', 'bar']);

        $this->assertEquals(['foo' => 'bar'], $media->getOptions(), '->addOption() adds an option');

        $r->invokeArgs($media, ['baz', 'bat']);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat',
            ],
            $media->getOptions(),
            '->addOption() appends the option to the existing ones'
        );

        $message = '->addOption() raises an exception when the specified option already exists';

        try {
            $r->invokeArgs($media, ['baz', 'bat']);
            $this->fail($message);
        } catch (InvalidArgumentException $e) {
            $this->anything();
        }
    }

    public function testAddOptions(): void
    {
        $media = $this->getMockForAbstractClass(AbstractGenerator::class, [], '', false);

        $this->assertEquals([], $media->getOptions());

        $r = new ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, [['foo' => 'bar', 'baz' => 'bat']]);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat',
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
                'bal' => 'bac',
            ],
            $media->getOptions(),
            '->addOptions() adds the given options to the existing ones'
        );

        $message = '->addOptions() raises an exception when one of the given options already exists';

        try {
            $r->invokeArgs($media, [['bak' => 'bam', 'bah' => 'bap', 'baz' => 'bat']]);
            $this->fail($message);
        } catch (InvalidArgumentException $e) {
            $this->anything();
        }
    }

    public function testSetOption(): void
    {
        $media = $this
            ->getMockBuilder(AbstractGenerator::class)
            ->setConstructorArgs(['/usr/local/bin/wkhtmltopdf'])
            ->getMockForAbstractClass()
        ;

        $logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->getMock()
        ;
        $media->setLogger($logger);
        $logger->expects($this->once())->method('debug');

        $r = new ReflectionMethod($media, 'addOption');
        $r->setAccessible(true);
        $r->invokeArgs($media, ['foo', 'bar']);

        $media->setOption('foo', 'abc');

        $this->assertEquals(
            [
                'foo' => 'abc',
            ],
            $media->getOptions(),
            '->setOption() defines the value of an option'
        );

        $message = '->setOption() raises an exception when the specified option does not exist';

        try {
            $media->setOption('bad', 'def');
            $this->fail($message);
        } catch (InvalidArgumentException $e) {
            $this->anything();
        }
    }

    public function testSetOptions(): void
    {
        $media = $this
            ->getMockBuilder(AbstractGenerator::class)
            ->setConstructorArgs(['/usr/local/bin/wkhtmltopdf'])
            ->getMockForAbstractClass()
        ;

        $logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->getMock()
        ;
        $media->setLogger($logger);
        $logger->expects($this->exactly(4))->method('debug');

        $r = new ReflectionMethod($media, 'addOptions');
        $r->setAccessible(true);
        $r->invokeArgs($media, [['foo' => 'bar', 'baz' => 'bat']]);

        $media->setOptions(['foo' => 'abc', 'baz' => 'def']);

        $this->assertEquals(
            [
                'foo' => 'abc',
                'baz' => 'def',
            ],
            $media->getOptions(),
            '->setOptions() defines the values of all the specified options'
        );

        $message = '->setOptions() raises an exception when one of the specified options does not exist';

        try {
            $media->setOptions(['foo' => 'abc', 'baz' => 'def', 'bad' => 'ghi']);
            $this->fail($message);
        } catch (InvalidArgumentException $e) {
            $this->anything();
        }
    }

    public function testGenerate(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'prepareOutput',
                'getCommand',
                'executeCommand',
                'checkOutput',
                'checkProcessStatus',
            ])
            ->setConstructorArgs(['the_binary', []])
            ->getMock()
        ;

        $logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->getMock()
        ;
        $media->setLogger($logger);
        $logger
            ->expects($this->exactly(2))
            ->method('info')
            ->with(
                $this->logicalOr(
                    'Generate from file(s) "the_input_file" to file "the_output_file".',
                    'File "the_output_file" has been successfully generated.'
                ),
                $this->logicalOr(
                    ['command' => 'the command', 'env' => null, 'timeout' => false],
                    ['command' => 'the command', 'stdout' => 'stdout', 'stderr' => 'stderr']
                )
            )
        ;

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
            ->willReturn([0, 'stdout', 'stderr'])
        ;
        $media
            ->expects($this->once())
            ->method('checkProcessStatus')
            ->with(0, 'stdout', 'stderr', 'the command')
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

    public function testFailingGenerate(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'prepareOutput',
                'getCommand',
                'executeCommand',
                'checkOutput',
                'checkProcessStatus',
            ])
            ->setConstructorArgs(['the_binary', [], ['PATH' => '/usr/bin']])
            ->getMock()
        ;

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $media->setLogger($logger);
        $media->setTimeout(2000);

        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo('Generate from file(s) "the_input_file" to file "the_output_file".'),
                $this->equalTo(['command' => 'the command', 'env' => ['PATH' => '/usr/bin'], 'timeout' => 2000])
            )
        ;

        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('An error happened while generating "the_output_file".'),
                $this->equalTo(['command' => 'the command', 'status' => 1, 'stdout' => 'stdout', 'stderr' => 'stderr'])
            )
        ;

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
                $this->equalTo('the_output_file')
            )
            ->will($this->returnValue('the command'))
        ;
        $media
            ->expects($this->once())
            ->method('executeCommand')
            ->with($this->equalTo('the command'))
            ->willReturn([1, 'stdout', 'stderr'])
        ;
        $media
            ->expects($this->once())
            ->method('checkProcessStatus')
            ->with(1, 'stdout', 'stderr', 'the command')
            ->willThrowException(new RuntimeException())
        ;

        $this->expectException(RuntimeException::class);

        $media->generate('the_input_file', 'the_output_file', ['foo' => 'bar']);
    }

    public function testGenerateFromHtml(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'generate',
                'createTemporaryFile',
            ])
            ->setConstructorArgs(['the_binary'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

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

    public function testGenerateFromHtmlWithHtmlArray(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'generate',
                'createTemporaryFile',
            ])
            ->setConstructorArgs(['the_binary'])
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

    public function testGetOutput(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'getDefaultExtension',
                'createTemporaryFile',
                'generate',
                'getFileContents',
                'unlink',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

    public function testGetOutputFromHtml(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'getOutput',
                'createTemporaryFile',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

    public function testGetOutputFromHtmlWithHtmlArray(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'getOutput',
                'createTemporaryFile',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

    public function testMergeOptions(): void
    {
        $media = $this->getMockForAbstractClass(AbstractGenerator::class, [], '', false);

        $originalOptions = ['foo' => 'bar', 'baz' => 'bat'];

        $addOptions = new ReflectionMethod($media, 'addOptions');
        $addOptions->setAccessible(true);
        $addOptions->invokeArgs($media, [$originalOptions]);

        $r = new ReflectionMethod($media, 'mergeOptions');
        $r->setAccessible(true);

        $mergedOptions = $r->invokeArgs($media, [['foo' => 'ban']]);

        $this->assertEquals(
            [
                'foo' => 'ban',
                'baz' => 'bat',
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
                'baz' => 'bag',
            ],
            $mergedOptions,
            '->mergeOptions() merges many options to the instance ones and returns the result options array'
        );

        $message = '->mergeOptions() throws an InvalidArgumentException once there is an undefined option in the given array';

        try {
            $r->invokeArgs($media, [['foo' => 'ban', 'bad' => 'bah']]);
            $this->fail($message);
        } catch (InvalidArgumentException $e) {
            $this->anything();
        }
    }

    /**
     * @dataProvider dataForBuildCommand
     */
    public function testBuildCommand(string $binary, string $url, string $path, array $options, string $expected): void
    {
        $media = $this->getMockForAbstractClass(AbstractGenerator::class, [], '', false);

        $r = new ReflectionMethod($media, 'buildCommand');
        $r->setAccessible(true);

        $this->assertEquals($expected, $r->invokeArgs($media, [$binary, $url, $path, $options]));
    }

    public function dataForBuildCommand(): array
    {
        $theBinary = $this->getPHPExecutableFromPath() . ' -v'; // i.e.: '/usr/bin/php -v'

        return [
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [],
                $theBinary . ' ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'foo' => null,
                    'bar' => false,
                    'baz' => [],
                ],
                $theBinary . ' ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'foo' => 'foovalue',
                    'bar' => ['barvalue1', 'barvalue2'],
                    'baz' => true,
                ],
                $theBinary . ' --foo ' . \escapeshellarg('foovalue') . ' --bar ' . \escapeshellarg('barvalue1') . ' --bar ' . \escapeshellarg('barvalue2') . ' --baz ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'cookie' => ['session' => 'bla', 'phpsess' => 12],
                    'no-background' => '1',
                ],
                $theBinary . ' --cookie ' . \escapeshellarg('session') . ' ' . \escapeshellarg('bla') . ' --cookie ' . \escapeshellarg('phpsess') . ' ' . \escapeshellarg('12') . ' --no-background ' . \escapeshellarg('1') . ' ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'allow' => ['/path1', '/path2'],
                    'no-background' => '1',
                ],
                $theBinary . ' --allow ' . \escapeshellarg('/path1') . ' --allow ' . \escapeshellarg('/path2') . ' --no-background ' . \escapeshellarg('1') . ' ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
            [
                $theBinary,
                'http://the.url/',
                '/the/path',
                [
                    'image-dpi' => 100,
                    'image-quality' => 50,
                ],
                $theBinary . ' ' . '--image-dpi 100 --image-quality 50 ' . \escapeshellarg('http://the.url/') . ' ' . \escapeshellarg('/the/path'),
            ],
        ];
    }

    public function testCheckOutput(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'fileExists',
                'filesize',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

        $r = new ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() checks both file existence and size';

        try {
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->anything();
        } catch (RuntimeException $e) {
            $this->fail($message);
        }
    }

    public function testCheckOutputWhenTheFileDoesNotExist(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'fileExists',
                'filesize',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $media
            ->expects($this->once())
            ->method('fileExists')
            ->with($this->equalTo('the_output_file'))
            ->will($this->returnValue(false))
        ;

        $r = new ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() throws an InvalidArgumentException when the file does not exist';

        try {
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->fail($message);
        } catch (RuntimeException $e) {
            $this->anything();
        }
    }

    public function testCheckOutputWhenTheFileIsEmpty(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'fileExists',
                'filesize',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;

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

        $r = new ReflectionMethod($media, 'checkOutput');
        $r->setAccessible(true);

        $message = '->checkOutput() throws an InvalidArgumentException when the file is empty';

        try {
            $r->invokeArgs($media, ['the_output_file', 'the command']);
            $this->fail($message);
        } catch (RuntimeException $e) {
            $this->anything();
        }
    }

    public function testCheckProcessStatus(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods(['configure'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $r = new ReflectionMethod($media, 'checkProcessStatus');
        $r->setAccessible(true);

        try {
            $r->invokeArgs($media, [0, '', '', 'the command']);
            $this->anything();
        } catch (RuntimeException $e) {
            $this->fail('0 status means success');
        }

        try {
            $r->invokeArgs($media, [1, '', '', 'the command']);
            $this->anything();
        } catch (RuntimeException $e) {
            $this->fail('1 status means failure, but no stderr content');
        }

        try {
            $r->invokeArgs($media, [1, '', 'Could not connect to X', 'the command']);
            $this->fail('1 status means failure');
        } catch (RuntimeException $e) {
            $this->assertEquals(1, $e->getCode(), 'Exception thrown by checkProcessStatus should pass on the error code');
        }
    }

    /**
     * @dataProvider dataForIsAssociativeArray
     */
    public function testIsAssociativeArray(array $array, bool $isAssociativeArray): void
    {
        $generator = $this->getMockForAbstractClass(AbstractGenerator::class, [], '', false);

        $r = new ReflectionMethod($generator, 'isAssociativeArray');
        $r->setAccessible(true);
        $this->assertEquals($isAssociativeArray, $r->invokeArgs($generator, [$array]));
    }

    /**
     * @expectedException Knp\Snappy\Exception\FileAlreadyExistsException
     */
    public function testItThrowsTheProperExceptionWhenFileExistsAndNotOverwritting(): void
    {
        $media = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'fileExists',
                'isFile',
            ])
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
        $r = new ReflectionMethod($media, 'prepareOutput');
        $r->setAccessible(true);

        $this->expectException(\Knp\Snappy\Exception\FileAlreadyExistsException::class);

        $r->invokeArgs($media, ['', false]);
    }

    public function dataForIsAssociativeArray(): array
    {
        return [
            [
                ['key' => 'value'],
                true,
            ],
            [
                ['key' => 2],
                true,
            ],
            [
                ['key' => 'value', 'key2' => 'value2'],
                true,
            ],
            [
                [0 => 'value', 1 => 'value2', 'deux' => 'value3'],
                true,
            ],
            [
                [0 => 'value'],
                false,
            ],
            [
                [0 => 'value', 1 => 'value2', 3 => 'value3'],
                false,
            ],
            [
                ['0' => 'value', '1' => 'value2', '3' => 'value3'],
                false,
            ],
            [
                [],
                false,
            ],
        ];
    }

    public function testCleanupEmptyTemporaryFiles(): void
    {
        $generator = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'unlink',
            ])
            ->setConstructorArgs(['the_binary'])
            ->getMock()
        ;

        $generator
            ->expects($this->once())
            ->method('unlink')
        ;

        $create = new ReflectionMethod($generator, 'createTemporaryFile');
        $create->setAccessible(true);
        $create->invoke($generator, null, null);

        $files = new ReflectionProperty($generator, 'temporaryFiles');
        $files->setAccessible(true);
        $this->assertCount(1, $files->getValue($generator));

        $remove = new ReflectionMethod($generator, 'removeTemporaryFiles');
        $remove->setAccessible(true);
        $remove->invoke($generator);
    }

    public function testleanupTemporaryFiles(): void
    {
        $generator = $this->getMockBuilder(AbstractGenerator::class)
            ->setMethods([
                'configure',
                'unlink',
            ])
            ->setConstructorArgs(['the_binary'])
            ->getMock()
        ;

        $generator
            ->expects($this->once())
            ->method('unlink')
        ;

        $create = new ReflectionMethod($generator, 'createTemporaryFile');
        $create->setAccessible(true);
        $create->invoke($generator, '<html/>', 'html');

        $files = new ReflectionProperty($generator, 'temporaryFiles');
        $files->setAccessible(true);
        $this->assertCount(1, $files->getValue($generator));

        $remove = new ReflectionMethod($generator, 'removeTemporaryFiles');
        $remove->setAccessible(true);
        $remove->invoke($generator);
    }

    public function testResetOptions(): void
    {
        $media = new class('/usr/local/bin/wkhtmltopdf') extends AbstractGenerator {
            protected function configure(): void
            {
                $this->addOptions([
                    'optionA' => null,
                    'optionB' => 'abc',
                ]);
            }
        };

        $media->setOption('optionA', 'bar');

        $this->assertEquals(
            [
                'optionA' => 'bar',
                'optionB' => 'abc',
            ],
            $media->getOptions()
        );

        $media->resetOptions();

        $this->assertEquals(
            [
                'optionA' => null,
                'optionB' => 'abc',
            ],
            $media->getOptions()
        );
    }

    /**
     * @return null|string
     */
    private function getPHPExecutableFromPath(): ?string
    {
        if (isset($_SERVER['_'])) {
            return $_SERVER['_'];
        }

        if (@\defined(\PHP_BINARY)) {
            return \PHP_BINARY;
        }

        if (false === \getenv('PATH')) {
            return null;
        }

        $paths = \explode(\PATH_SEPARATOR, \getenv('PATH'));
        foreach ($paths as $path) {
            // we need this for XAMPP (Windows)
            if (\strstr($path, 'php.exe') && isset($_SERVER['WINDIR']) && \file_exists($path) && \is_file($path)) {
                return $path;
            }
            $php_executable = $path . \DIRECTORY_SEPARATOR . 'php' . (isset($_SERVER['WINDIR']) ? '.exe' : '');
            if (\file_exists($php_executable) && \is_file($php_executable)) {
                return $php_executable;
            }
        }

        return null; // not found
    }
}
