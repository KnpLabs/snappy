<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

use PHPUnit\Framework\TestCase;

class CommandBuilderTest extends TestCase
{
    /**
     * @dataProvider argumentsProvider
     */
    public function testCommandBuilder(array $arguments, string $expected)
    {
        list($binary, $input, $options) = $arguments;
        $commandBuilder = new CommandBuilder();

        $this->assertSame($expected, $commandBuilder->buildCommand($binary, $input, $options));
    }

    public function argumentsProvider()
    {
        return [
            [
                ['./binary', 'file:///input', []],
                "./binary 'file:///input'",
            ],
            [
                ['./binary', 'file:///input', ['screenshot' => false, 'enable-viewport' => null, 'enable-gpu' => '']],
                "./binary 'file:///input'",
            ],
            [
                ['./binary', 'file:///input', ['headless' => true, 'screenshot' => '/tmp/screen.jpg']],
                "./binary --headless --screenshot='/tmp/screen.jpg' 'file:///input'",
            ],
            [
                ['./binary', 'file:///input', ['window-size' => [1024, 768]]],
                "./binary --window-size='1024,768' 'file:///input'",
            ],
        ];
    }
}
