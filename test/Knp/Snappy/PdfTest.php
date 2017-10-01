<?php

namespace Knp\Snappy;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    const SHELL_ARG_QUOTE_REGEX = '(?:"|\')'; // escapeshellarg produces double quotes on Windows, single quotes otherwise

    public function tearDown()
    {
        $directory = __DIR__ . '/i-dont-exist';

        if (file_exists($directory)) {
            $iterator = new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            );

            foreach ($iterator as $item) {
                unlink(strval($item));
            }

            rmdir($directory);
        }

        $htmlFiles = new \CallbackFilterIterator(
            new \DirectoryIterator(__DIR__),
            function ($filename) {
                return preg_match('/\.html$/', $filename) === 1;
            }
        );

        foreach ($htmlFiles as $file) {
            unlink($file->getPathname());
        }
    }

    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Pdf();
        $this->assertInstanceOf('\Knp\Snappy\Pdf', $testObject);
    }

    public function testThatSomethingUsingTmpFolder()
    {
        $q = self::SHELL_ARG_QUOTE_REGEX;
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', ['footer-html' => 'footer']);
        $this->assertRegExp('/emptyBinary --lowquality --footer-html ' . $q . '.*' . $q . ' ' . $q . '.*' . $q . ' ' . $q . '.*' . $q . '/', $testObject->getLastCommand());
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(array $options, $expectedRegex)
    {
        $testObject = new PdfSpy();
        $testObject->getOutputFromHtml('<html></html>', $options);
        $this->assertRegExp($expectedRegex, $testObject->getLastCommand());
    }

    public function dataOptions()
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
                '/emptyBinary --lowquality --footer-html ' . $q . preg_quote(__FILE__, '/') . $q . ' ' . $q . '.*\.html' . $q . ' ' . $q . '.*\.pdf' . $q . '/',
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
        ];
    }

    public function testRemovesLocalFilesOnDestruct()
    {
        $pdf = new PdfSpy();
        $method = new \ReflectionMethod($pdf, 'createTemporaryFile');
        $method->setAccessible(true);
        $method->invoke($pdf, 'test', $pdf->getDefaultExtension());
        $this->assertEquals(1, count($pdf->temporaryFiles));
        $this->assertTrue(file_exists(reset($pdf->temporaryFiles)));
        $pdf->__destruct();
        $this->assertFalse(file_exists(reset($pdf->temporaryFiles)));
    }

    public function testRemovesLocalFilesOnError()
    {
        $pdf = new PdfSpy();
        $method = new \ReflectionMethod($pdf, 'createTemporaryFile');
        $method->setAccessible(true);
        $method->invoke($pdf, 'test', $pdf->getDefaultExtension());
        $this->assertEquals(1, count($pdf->temporaryFiles));

        $this->expectException(Error::class);
        trigger_error('test error', E_USER_ERROR);

        $this->assertFalse(file_exists(reset($pdf->temporaryFiles)));
    }
}

class PdfSpy extends Pdf
{
    private $lastCommand;

    public function __construct()
    {
        parent::__construct('emptyBinary');
    }

    public function getLastCommand()
    {
        return $this->lastCommand;
    }

    protected function executeCommand($command)
    {
        $this->lastCommand = $command;

        return [0, 'output', 'errorOutput'];
    }

    protected function checkOutput($output, $command)
    {
        //let's say everything went right
    }

    public function getOutput($input, array $options = [])
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());
        $this->generate($input, $filename, $options, true);

        return 'output';
    }
}
