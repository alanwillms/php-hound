<?php
namespace tests\output;

use phphound\Command;
use phphound\output\JsonOutput;

class JsonOutputTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_outputs_results_correctly()
    {
        $data = [
            'File.php' => [
                93 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
            ],
        ];

        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out'])
            ->getMock()
        ;
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new JsonOutput($cli);

        $result->expects($this->once())->method('toArray')->willReturn($data);
        $cli->expects($this->once())->method('out')->with(json_encode($data));

        $output->result($result);
    }

    /** @test */
    public function it_doesnt_output_on_starting_analysis()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new JsonOutput($cli);
        $output->trigger(Command::EVENT_STARTING_ANALYSIS);
    }

    /** @test */
    public function it_doesnt_output_on_starting_tool()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new JsonOutput($cli);
        $output->trigger(Command::EVENT_STARTING_TOOL, 'Toolname');
    }

    /** @test */
    public function it_doesnt_output_on_finishing_tool()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new JsonOutput($cli);
        $output->trigger(Command::EVENT_FINISHED_TOOL);
    }

    /** @test */
    public function it_doesnt_output_on_finishing_analysis()
    {
        $cli = $this->getMock('League\CLImate\CLImate');
        $cli->expects($this->never())->method($this->anything());;
        $output = new JsonOutput($cli);
        $output->trigger(Command::EVENT_FINISHED_ANALYSIS);
    }
}