<?php

namespace Knp\Snappy;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    const SHELL_ARG_QUOTE_REGEX = '(?:"|\')'; // escapeshellarg produces double quotes on Windows, single quotes otherwise

    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Pdf();
        $this->assertInstanceOf('\Knp\Snappy\Pdf', $testObject);
    }

    public function testThatSomething()
    {
        $q = self::SHELL_ARG_QUOTE_REGEX;
        $testObject = new PdfSpy();

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
        $this->assertRegExp('/emptyBinary --lowquality --footer-html '.$q.'.*'.$q.' '.$q.'.*'.$q.' '.$q.'.*'.$q.'/', $testObject->getLastCommand());

        $testObject->getOutputFromHtml('<html></html>', array());
        $this->assertRegExp('/emptyBinary --lowquality '.$q.'.*'.$q.' '.$q.'.*'.$q.'/', $testObject->getLastCommand());
    }

    public function testThatSomethingUsingTmpFolder()
    {
        $q = self::SHELL_ARG_QUOTE_REGEX;
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
        $this->assertRegExp('/emptyBinary --lowquality --footer-html '.$q.'.*'.$q.' '.$q.'.*'.$q.' '.$q.'.*'.$q.'/', $testObject->getLastCommand());
    }

    public function testThatSomethingUsingNonexistentTmpFolder()
    {
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__ . '/i-dont-exist');

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
    }

    public function testOptionsAreCorrectlySavedIfItIsLocalOrRemoteContent()
    {
        $q = self::SHELL_ARG_QUOTE_REGEX;
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer', 'xsl-style-sheet' => 'http://google.com'));
        $this->assertRegExp('/emptyBinary --lowquality --footer-html '.$q.'.*\.html'.$q.' --xsl-style-sheet '.$q.'.*\.xsl'.$q.' '.$q.'.*\.html'.$q.' '.$q.'.*\.pdf'.$q.'/', $testObject->getLastCommand());
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

        $this->setExpectedException('PHPUnit_Framework_Error');
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
        return array(0, 'output', 'errorOutput');
    }

    protected function checkOutput($output, $command)
    {
        //let's say everything went right
    }

    public function getOutput($input, array $options = array())
    {
        $filename = $this->createTemporaryFile(null, $this->getDefaultExtension());
        $this->generate($input, $filename, $options, true);

        return "output";
    }
}
