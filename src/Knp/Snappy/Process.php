<?php
namespace Knp\Snappy;

/**
 * Process a command, this class is a fallback if user has not
 * symfony component process
 *
 * @package Snappy
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>
 */
class Process
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var integer
     */
    private $exitCode;

    /**
     * @var string
     */
    private $errorOutput;

    /**
     * @var string
     */
    private $output;

    /**
     * @param string $command command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * run the command defined on the constructor
     */
    public function run()
    {
        $descriptorspec = array(
            1 => array('pipe', 'w'),  // stdout is a pipe that the child will write to
            2 => array('pipe', 'a') // stderr is a pipe that the child will append to
        );

        $process = proc_open($this->command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // 2 => readable handle connected to child stderr

            $this->output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $this->errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $this->exitCode = proc_close($process);
        }
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
}
