<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BackendTest extends TestCase
{
    public function testRunCommand()
    {
        $backend = new Backend('/bin/true');

        $commandBuilder = $this->getMockBuilder(CommandBuilder::class)
            ->setMethods(['buildCommand'])
            ->getMock();
        $commandBuilder->expects($this->once())
            ->method('buildCommand')
            ->with('/bin/true', 'file:///input', ['headless' => true])
            ->willReturn('/bin/true');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info');

        $backend->setCommandBuilder($commandBuilder);
        $backend->setLogger($logger);

        $this->assertNull($backend->run('file:///input', ['headless' => true]));
    }

    public function testRunThrowsAnExceptionWhenExecutionFail()
    {
        $backend = new Backend('/bin/false');

        $commandBuilder = $this->getMockBuilder(CommandBuilder::class)
            ->setMethods(['buildCommand'])
            ->getMock();
        $commandBuilder->expects($this->once())
            ->method('buildCommand')
            ->willReturn('/bin/false');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info');
        $logger->expects($this->once())
            ->method('error');

        $backend->setCommandBuilder($commandBuilder);
        $backend->setLogger($logger);

        $this->expectException(\RuntimeException::class);

        $backend->run('file:///input', ['headless' => true]);
    }
}
