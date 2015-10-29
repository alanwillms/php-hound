<?php
namespace tests\output;

use phphound\Analyser;
use phphound\output\HtmlOutput;

class HtmlOutputTest extends \PHPUnit_Framework_TestCase
{
    protected $runningScriptDir;

    protected function setUp()
    {
        $this->runningScriptDir = __DIR__ . '/../tmp/';
    }

    protected function tearDown()
    {
        if (file_exists($this->runningScriptDir . 'phphound')) {
            $this->recursiveRemoveDirectory($this->runningScriptDir . 'phphound');
        }
    }

    protected function recursiveRemoveDirectory($directory)
    {
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->recursiveRemoveDirectory($file);
                continue;
            }
            unlink($file);
        }
        rmdir($directory);
    }

    /** @test */
    public function it_outputs_results_correctly()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out', 'inline'])
            ->getMock()
        ;
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new HtmlOutput($cli, $this->runningScriptDir);

        $result->expects($this->any())->method('toArray')->willReturn([
            realpath(__DIR__ . '/../data/File.php') => [
                2 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
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
            ->setMethods(['green', 'red', 'out'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, $this->runningScriptDir);

        $cli->expects($this->at(0))->method('green')->with('Starting analysis');
        $cli->expects($this->at(1))->method('out')->with('(ignored paths:)');
        $cli->expects($this->at(2))->method('red')->with("\tvendor");
        $cli->expects($this->at(3))->method('red')->with("\ttests");

        $output->trigger(
            Analyser::EVENT_STARTING_ANALYSIS,
            ['ignoredPaths' => ['vendor', 'tests']]
        );
    }

    /** @test */
    public function it_outputs_on_starting_tool()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['inline'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, $this->runningScriptDir);
        $message = ['description' => 'Toolname'];

        $cli->expects($this->once())->method('inline')->with('Running Toolname... ');

        $output->trigger(Analyser::EVENT_STARTING_TOOL, $message);
    }

    /** @test */
    public function it_outputs_on_finishing_tool()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, $this->runningScriptDir);

        $cli->expects($this->once())->method('out')->with('Done!');

        $output->trigger(Analyser::EVENT_FINISHED_TOOL);
    }

    /** @test */
    public function it_outputs_on_finishing_analysis()
    {
        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['green'])
            ->getMock()
        ;
        $output = new HtmlOutput($cli, $this->runningScriptDir);

        $cli->expects($this->once())->method('green')->with('Analysis complete!');

        $output->trigger(Analyser::EVENT_FINISHED_ANALYSIS);
    }
}
