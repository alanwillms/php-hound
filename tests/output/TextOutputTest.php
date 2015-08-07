<?php
namespace tests\output;

use phphound\output\TextOutput;

class TextOutputTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_outputs_correctly_the_results()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['yellowFlank'])
            ->getMock();
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new TextOutput($cli);

        $result->expects($this->once())->method('toArray')->willReturn([
            'File.php' => [
                93 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
            ],
        ]);

        $cli->expects($this->any())->method('yellowFlank')->with('File.php', '=', 2);
        $cli->expects($this->any())->method('cyanInline')->with('93: ');
        $cli->expects($this->any())->method('inline')->with('Error trimmed');

        $output->result($result);
    }
}