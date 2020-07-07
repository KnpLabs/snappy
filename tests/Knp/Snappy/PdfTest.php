<?php

namespace Tests\Knp\Snappy;

use Knp\Snappy\Pdf;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use CallbackFilterIterator;
use DirectoryIterator;
use ReflectionMethod;

class PdfTest extends TestCase
{
    public function tearDown(): void
    {
        $directory = __DIR__ . '/i-dont-exist';

        if (\file_exists($directory)) {
            $iterator = new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
            );

            foreach ($iterator as $item) {
                \unlink((string) $item);
            }

            \rmdir($directory);
        }

        $htmlFiles = new CallbackFilterIterator(
            new DirectoryIterator(__DIR__),
            function ($filename) {
                return \preg_match('/\.html$/', $filename) === 1;
            }
        );

        foreach ($htmlFiles as $file) {
            \unlink($file->getPathname());
        }
    }

    public function testCreateInstance(): void
    {
        $testObject = new Pdf();
        $this->assertInstanceOf(Pdf::class, $testObject);
    }

    public function testThatSomethingUsingTmpFolder(): void
    {
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', ['footer-html' => 'footer']);

        \array_map(function (string $expectedRegex, string $commandPart) {
            $this->assertRegExp($expectedRegex, $commandPart);
        }, ['/emptyBinary/', '/--lowquality/', '/--footer-html/', '/.*\.html/', '/.*\.html/', '/.*\.pdf/'], $testObject->getLastCommand());
    }

    public function testThatSomethingUsingNonexistentTmpFolder(): void
    {
        $temporaryFolder = \sys_get_temp_dir() . '/i-dont-exist';

        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder($temporaryFolder);

        $testObject->getOutputFromHtml('<html></html>', ['footer-html' => 'footer']);

        $this->assertDirectoryExists($temporaryFolder);
    }

    public function testRemovesLocalFilesOnError(): void
    {
        $pdf = new PdfSpy();
        $method = new ReflectionMethod($pdf, 'createTemporaryFile');
        $method->setAccessible(true);
        $method->invoke($pdf, 'test', $pdf->getDefaultExtension());
        $this->assertEquals(1, \count($pdf->temporaryFiles));
        $this->expectException(Error::class);
        \trigger_error('test error', \E_USER_ERROR);
        $this->assertFileNotExists(\reset($pdf->temporaryFiles));
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(array $options, array $expectedRegexes): void
    {
        $testObject = new PdfSpy();
        $testObject->getOutputFromHtml('<html></html>', $options);

        \array_map(function (string $expectedRegex, string $commandPart) {
            $this->assertRegExp($expectedRegex, $commandPart);
        }, $expectedRegexes, $testObject->getLastCommand());
    }

    public function dataOptions(): array
    {
        return [
            // no options
            [
                [],
                ['/emptyBinary/', '/--lowquality/', '/.*\.html/', '/.*\.pdf/'],
            ],
            // just pass the given footer URL
            [
                ['footer-html' => 'http://google.com'],
                ['/emptyBinary/', '/--lowquality/', '/--footer-html/', '/' . \preg_quote('http://google.com', '/') . '/', '/.*\.html/', '/.*\.pdf/'],
            ],
            // just pass the given footer file
            [
                ['footer-html' => __FILE__],
                ['/emptyBinary/', '/--lowquality/', '/--footer-html/', '/' . \preg_quote(__FILE__, '/') . '/', '/.*\.html/', '/.*\.pdf/'],
            ],
            // save the given footer HTML string into a temporary file and pass that filename
            [
                ['footer-html' => 'footer'],
                ['/emptyBinary/', '/--lowquality/', '/--footer-html/', '/.*\.html/', '/.*\.html/', '/.*\.pdf/'],
            ],
            // save the content of the given XSL URL to a file and pass that filename
            [
                ['xsl-style-sheet' => 'http://google.com'],
                ['/emptyBinary/', '/--lowquality/', '/--xsl-style-sheet/', '/.*\.xsl/', '/.*\.html/', '/.*\.pdf/'],
            ],
            // set toc options after toc argument
            [
                [
                    'grayscale' => true,
                    'toc' => true,
                    'disable-dotted-lines' => true,
                ],
                ['/emptyBinary/', '/--grayscale/', '/--lowquality/', '/toc/', '/--disable-dotted-lines/', '/.*\.html/', '/.*\.pdf/'],
            ],
        ];
    }

    public function testRemovesLocalFilesOnDestruct(): void
    {
        $pdf = new PdfSpy();
        $method = new ReflectionMethod($pdf, 'createTemporaryFile');
        $method->setAccessible(true);
        $method->invoke($pdf, 'test', $pdf->getDefaultExtension());
        $this->assertEquals(1, \count($pdf->temporaryFiles));
        $this->assertFileExists(\reset($pdf->temporaryFiles));
        $pdf->__destruct();
        $this->assertFileNotExists(\reset($pdf->temporaryFiles));
    }
}

class PdfSpy extends Pdf
{
    /**
     * @var string[]
     */
    private $lastCommand;

    public function __construct()
    {
        parent::__construct('emptyBinary');
    }

    public function getLastCommand(): array
    {
        return $this->lastCommand;
    }

    public function getOutput($input, array $options = []): string
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());
        $this->generate($input, $filename, $options, true);

        return 'output';
    }

    protected function runProcess(Process $process, array $command): array
    {
        $this->lastCommand = $command;

        return [0, 'output', 'errorOutput'];
    }

    protected function checkOutput(string $output, string $command): void
    {
        //let's say everything went right
    }
}
