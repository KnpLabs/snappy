<?php

namespace Tests\Knp\Snappy;

use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use CallbackFilterIterator;
use DirectoryIterator;
use ReflectionMethod;

class PdfTest extends TestCase
{
    const SHELL_ARG_QUOTE_REGEX = '(?:"|\')'; // escapeshellarg produces double quotes on Windows, single quotes otherwise

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
        $q = self::SHELL_ARG_QUOTE_REGEX;
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', ['footer-html' => 'footer']);
        $this->assertMatchesRegularExpression('/emptyBinary --lowquality --footer-html ' . $q . '.*' . $q . ' ' . $q . '.*' . $q . ' ' . $q . '.*' . $q . '/', $testObject->getLastCommand());
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
        $this->assertCount(1, $pdf->temporaryFiles);
        $this->expectError();
        \trigger_error('test error', \E_USER_ERROR);
        #$this->assertFileDoesNotExist(\reset($pdf->temporaryFiles)); # TODO Unreachable Code. What Todo here?
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(array $options, string $expectedRegex): void
    {
        $testObject = new PdfSpy();
        $testObject->getOutputFromHtml('<html></html>', $options);
        $this->assertMatchesRegularExpression($expectedRegex, $testObject->getLastCommand());
    }

    public function dataOptions(): array
    {
        $q = self::SHELL_ARG_QUOTE_REGEX;

        return [
            // no options
            [
                [],
                '/emptyBinary --lowquality ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
            // just pass the given footer URL
            [
                ['footer-html' => 'http://google.com'],
                '/emptyBinary --lowquality --footer-html ' . $q . 'http:\/\/google\.com' . $q . ' ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
            // just pass the given footer file
            [
                ['footer-html' => __FILE__],
                '/emptyBinary --lowquality --footer-html ' . $q . \preg_quote(__FILE__, '/') . $q . ' ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
            // save the given footer HTML string into a temporary file and pass that filename
            [
                ['footer-html' => 'footer'],
                '/emptyBinary --lowquality --footer-html ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
            // save the content of the given XSL URL to a file and pass that filename
            [
                ['xsl-style-sheet' => 'http://google.com'],
                '/emptyBinary --lowquality --xsl-style-sheet ' . $q . '.*\.xsl' . $q . ' ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
            // set toc options after toc argument
            [
                [
                    'grayscale' => true,
                    'toc' => true,
                    'disable-dotted-lines' => true,
                ],
                '/emptyBinary --grayscale --lowquality toc --disable-dotted-lines ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
            ],
        ];
    }

    public function testRemovesLocalFilesOnDestruct(): void
    {
        $pdf = new PdfSpy();
        $method = new ReflectionMethod($pdf, 'createTemporaryFile');
        $method->setAccessible(true);
        $method->invoke($pdf, 'test', $pdf->getDefaultExtension());
        $this->assertCount(1, $pdf->temporaryFiles);
        $this->assertFileExists(\reset($pdf->temporaryFiles));
        $pdf->__destruct();
        $this->assertFileDoesNotExist(\reset($pdf->temporaryFiles));
    }
}

class PdfSpy extends Pdf
{
    private string $lastCommand;

    public function __construct()
    {
        parent::__construct('emptyBinary');
    }

    public function getLastCommand(): string
    {
        return $this->lastCommand;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(array|string $input, array $options = []): string
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());
        $this->generate($input, $filename, $options, true);

        return 'output';
    }

    /**
     * {@inheritdoc}
     */
    protected function executeCommand(string $command): array
    {
        $this->lastCommand = $command;

        return [0, 'output', 'errorOutput'];
    }

    /**
     * {@inheritdoc}
     */
    protected function checkOutput(string $output, string $command): void
    {
        //let's say everything went right
    }
}
