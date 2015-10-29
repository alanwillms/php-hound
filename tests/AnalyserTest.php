<?php
namespace tests;

use League\CLImate\CLImate;
use phphound\Analyser;
use phphound\AnalysisResult;

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

    // /** @test */
    // public function it_accepts_target_path()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound', 'target/path'];
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals('target/path', $command->getAnalysedPath());
    // }
    //
    // /** @test */
    // public function it_uses_current_dir_as_target_path_when_not_informed()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound'];
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals('.', $command->getAnalysedPath());
    // }
    //
    // /** @test */
    // public function it_accepts_ignore_param()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound', '--ignore=dir'];
    //
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals(['dir'], $command->getIgnoredPaths());
    // }
    //
    // /** @test */
    // public function it_accepts_ignore_param_with_multiple_directories()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound', '--ignore=dir1,dir2'];
    //
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals(['dir1', 'dir2'], $command->getIgnoredPaths());
    // }
    //
    // /** @test */
    // public function it_has_ignore_param_default()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound'];
    //
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals(['vendor', 'tests', 'features', 'spec'], $command->getIgnoredPaths());
    // }
    //
    // /** @test */
    // public function it_accepts_ignore_param_with_empty_value()
    // {
    //     $cli = $this->getMock('League\CLImate\CLImate', ['output']);
    //     $arguments = ['php-hound', '--ignore='];
    //
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //     $this->assertEquals([], $command->getIgnoredPaths());
    // }
    //
    // /** @test */
    // public function it_does_not_accept_invalid_format_value()
    // {
    //     $this->setExpectedException('UnexpectedValueException');
    //     $arguments = ['php-hound', '--format=invalid'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    // }
    //
    // /** @test */
    // public function it_uses_json_output_with_format_json_param()
    // {
    //     $arguments = ['php-hound', '--format=json'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\JsonOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_json_output_with_f_json_param()
    // {
    //     $arguments = ['php-hound', '-f=json'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\JsonOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_xml_output_with_format_xml_param()
    // {
    //     $arguments = ['php-hound', '--format=xml'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\XmlOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_xml_output_with_f_xml_param()
    // {
    //     $arguments = ['php-hound', '-f=xml'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\XmlOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_csv_output_with_format_csv_param()
    // {
    //     $arguments = ['php-hound', '--format=csv'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\CsvOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_csv_output_with_f_csv_param()
    // {
    //     $arguments = ['php-hound', '-f=csv'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\CsvOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_html_output_with_format_html_param()
    // {
    //     $arguments = ['php-hound', '--format=html'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\HtmlOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
    //
    // /** @test */
    // public function it_uses_html_output_with_f_html_param()
    // {
    //     $arguments = ['php-hound', '-f=html'];
    //     $cli = new CLImate;
    //     $command = new Command($cli, $this->binariesPath, $arguments);
    //
    //     $this->assertInstanceOf('phphound\output\HtmlOutput', \PHPUnit_Framework_Assert::readAttribute($command, 'output'));
    // }
}
