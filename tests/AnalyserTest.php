<?php
namespace tests;

use League\CLImate\CLImate;
use phphound\Analyser;
use phphound\AnalysisResult;
use phphound\output\filter\DiffOutputFilter;

class AnalyserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $this->cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $this->output = $this
            ->getMockBuilder('phphound\output\TextOutput')
            ->setConstructorArgs([$this->cli, $tmp])
            ->getMock()
        ;
        $this->binariesPath = $tmp;
        $commands = ['phpcs', 'phpcpd', 'phpmd'];
        foreach ($commands as $command) {
            $commandPath = $this->binariesPath . $command;
            touch($commandPath);
            chmod($commandPath, 0777);
        }
    }

    /** @test */
    public function it_runs_analysis_tools()
    {
        $analyser = $this
            ->getMockBuilder('phphound\Analyser')
            ->setMethods(['getAnalysisTools'])
            ->setConstructorArgs([
                $this->output,
                $this->binariesPath,
                '.',
                []
            ])
            ->getMock()
        ;
        $tool = $this
            ->getMockBuilder('phphound\integration\PHPCodeSniffer')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $analyser
            ->expects($this->once())
            ->method('getAnalysisTools')
            ->willReturn([$tool])
        ;
        $tool
            ->expects($this->once())
            ->method('run')
            ->willReturn(new AnalysisResult)
        ;
        $tool
            ->method('getAnalysisResult')
            ->willReturn(new AnalysisResult)
        ;

        $analyser->run();
    }

    /** @test **/
    public function it_delegates_results_filter_to_analysis_result()
    {
        $filter = new DiffOutputFilter('', []);
        $result = $this->getMock('phphound\AnalysisResult', ['setResultsFilter']);
        $result->expects($this->once())->method('setResultsFilter')->with($filter);

        $analyser = $this
            ->getMockBuilder('phphound\Analyser')
            ->setMethods(['getAnalysisTools', 'createResult'])
            ->setConstructorArgs([
                $this->output,
                $this->binariesPath,
                '.',
                []
            ])
            ->getMock()
        ;
        $analyser
            ->expects($this->once())
            ->method('getAnalysisTools')
            ->willReturn([])
        ;
        $analyser
            ->expects($this->once())
            ->method('createResult')
            ->willReturn($result)
        ;
        $analyser->setResultsFilter($filter);
        $analyser->run();
    }
}
