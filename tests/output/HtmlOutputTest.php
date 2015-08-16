<?php
namespace tests\output;

use phphound\Command;
use phphound\output\HtmlOutput;

class HtmlOutputTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_outputs_results_correctly()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out', 'inline'])
            ->getMock()
        ;
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new HtmlOutput($cli, sys_get_temp_dir());

        $result->expects($this->once())->method('toArray')->willReturn([
            'File.php' => [
                93 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
            ],
        ]);

        $cli->expects($this->once())->method('inline')->with('Writing HTML report in "./phphound/"... ');
        $cli->expects($this->any())->method('out')->with('Done!');

        $output->result($result);
    }

    /** @test */
    public function it_outputs_on_starting_analysis()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['green'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, sys_get_temp_dir());

        $cli->expects($this->once())->method('green')->with('Starting analysis');

        $output->trigger(Command::EVENT_STARTING_ANALYSIS);
    }

    /** @test */
    public function it_outputs_on_starting_tool()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['inline'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, sys_get_temp_dir());

        $cli->expects($this->once())->method('inline')->with('Running Toolname... ');

        $output->trigger(Command::EVENT_STARTING_TOOL, 'Toolname');
    }

    /** @test */
    public function it_outputs_on_finishing_tool()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, sys_get_temp_dir());

        $cli->expects($this->once())->method('out')->with('Done!');

        $output->trigger(Command::EVENT_FINISHED_TOOL);
    }

    /** @test */
    public function it_outputs_on_finishing_analysis()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['green'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, sys_get_temp_dir());

        $cli->expects($this->once())->method('green')->with('Analysis complete!');

        $output->trigger(Command::EVENT_FINISHED_ANALYSIS);
    }
}