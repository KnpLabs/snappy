<?php

namespace Knp\Snappy;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $testObject = new \Knp\Snappy\Pdf();
        $this->assertInstanceOf('\Knp\Snappy\Pdf', $testObject);
    }

    public function testThatSomething()
    {
        $testObject = new PdfSpy();

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
        $this->assertRegExp("/emptyBinary --lowquality --footer-html '.*' '.*' '.*'/", $testObject->getLastCommand());

        $testObject->getOutputFromHtml('<html></html>', array());
        $this->assertRegExp("/emptyBinary --lowquality '.*' '.*'/", $testObject->getLastCommand());
    }

    public function testThatSomethingUsingTmpFolder()
    {
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__);

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
        $this->assertRegExp("/emptyBinary --lowquality --footer-html '.*' '.*' '.*'/", $testObject->getLastCommand());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testThatSomethingUsingWrongTmpFolder()
    {
        $testObject = new PdfSpy();
        $testObject->setTemporaryFolder(__DIR__.'/i-dont-exist');

        $testObject->getOutputFromHtml('<html></html>', array('footer-html' => 'footer'));
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
        $this->generate($input, $filename, $options);

        return "output";
    }
}
