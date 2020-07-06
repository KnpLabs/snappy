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
    const SHELL_ARG_QUOTE_REGEX = '(?:"|\')'; // escapeshellarg produces double quotes on Windows, single quotes otherwise

    /**
     * @var string
     */
    private static $commandPartDelimiter;

    public static function setUpBeforeClass(): void
    {
        self::$commandPartDelimiter = '\\' !== \DIRECTORY_SEPARATOR ? "'" : ''; // command parts which are not quoted on Windows are enclosed by single quotes on Linux
    }

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
        $d = self::$commandPartDelimiter;
        $this->assertRegExp("/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}--footer-html{$d} {$d}.*{$d} {$d}.*{$d} {$d}.*{$d}/", $testObject->getLastCommand());
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
    public function testOptions(array $options, string $expectedRegex): void
    {
        $testObject = new PdfSpy();
        $testObject->getOutputFromHtml('<html></html>', $options);
        $this->assertRegExp($expectedRegex, $testObject->getLastCommand());
    }

    public function dataOptions(): array
    {
        $d = self::$commandPartDelimiter;
        $q = self::SHELL_ARG_QUOTE_REGEX;

        return [
            // no options
            [
                [],
                "/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
            ],
            // just pass the given footer URL
            [
                ['footer-html' => 'http://google.com'],
                "/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}--footer-html{$d} {$q}" . \preg_quote('http://google.com', '/') . "{$q} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
            ],
            // just pass the given footer file
            [
                ['footer-html' => __FILE__],
                "/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}--footer-html{$d} {$d}" . \preg_quote(__FILE__, '/') . "{$d} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
            ],
            // save the given footer HTML string into a temporary file and pass that filename
            [
                ['footer-html' => 'footer'],
                "/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}--footer-html{$d} {$d}.*\.html{$d} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
            ],
            // save the content of the given XSL URL to a file and pass that filename
            [
                ['xsl-style-sheet' => 'http://google.com'],
                "/{$d}emptyBinary{$d} {$d}--lowquality{$d} {$d}--xsl-style-sheet{$d} {$d}.*\.xsl{$d} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
            ],
            // set toc options after toc argument
            [
                [
                    'grayscale' => true,
                    'toc' => true,
                    'disable-dotted-lines' => true,
                ],
                "/{$d}emptyBinary{$d} {$d}--grayscale{$d} {$d}--lowquality{$d} {$d}toc{$d} {$d}--disable-dotted-lines{$d} {$d}.*\.html{$d} {$d}.*\.pdf{$d}/",
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
     * @var string
     */
    private $lastCommand;

    public function __construct()
    {
        parent::__construct('emptyBinary');
    }

    public function getLastCommand(): string
    {
        return $this->lastCommand;
    }

    public function getOutput($input, array $options = []): string
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());
        $this->generate($input, $filename, $options, true);

        return 'output';
    }

    protected function runProcess(Process $process): array
    {
        $this->lastCommand = $process->getCommandLine();

        return [0, 'output', 'errorOutput'];
    }

    protected function checkOutput(string $output, string $command): void
    {
        //let's say everything went right
    }
}
