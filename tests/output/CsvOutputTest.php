<?php
namespace tests\output;

use phphound\Command;
use phphound\output\CsvOutput;

class CsvOutputTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_outputs_results_correctly()
    {
        $data = [
            'File.php' => [
                93 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
            ],
        ];
        $csv = implode("\n", [
            'File,Line,Tool,Type,Message',
            'File.php,93,PHP-Hound,error,"Error trimmed"',
            '',
        ]);

        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out'])
            ->getMock()
        ;
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new CsvOutput($cli);

        $result->expects($this->once())->method('toArray')->willReturn($data);
        $cli->expects($this->once())->method('out')->with($csv);

        $output->result($result);
    }

    /** @test */
    public function it_doesnt_output_on_starting_analysis()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new CsvOutput($cli);
        $output->trigger(Command::EVENT_STARTING_ANALYSIS);
    }

    /** @test */
    public function it_doesnt_output_on_starting_tool()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new CsvOutput($cli);
        $output->trigger(Command::EVENT_STARTING_TOOL, 'Toolname');
    }

    /** @test */
    public function it_doesnt_output_on_finishing_tool()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new CsvOutput($cli);
        $output->trigger(Command::EVENT_FINISHED_TOOL);
    }

    /** @test */
    public function it_doesnt_output_on_finishing_analysis()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new CsvOutput($cli);
        $output->trigger(Command::EVENT_FINISHED_ANALYSIS);
    }
}