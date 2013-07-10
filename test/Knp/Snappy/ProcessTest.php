<?php

namespace Knp\Snappy;

use Knp\Snappy\Process;
use Symfony\Component\Process\Process as SymfonyProcess;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     **/
    public function it_should_use_custom_environment_variables_for_process()
    {
        $process = new Process('echo $PATH', array('PATH' => '/'));

        $process->run();
        $this->assertEquals("/\n", $process->getOutput(), 'PATH env should be modified');
    }

    /**
     * @test
     **/
    public function it_should_use_inherited_environment_variables_for_process()
    {
        $process = new Process('echo $PATH');

        $process->run();
        $this->assertNotEquals("/\n", $process->getOutput(), 'PATH env should not be modified');
    }
}
