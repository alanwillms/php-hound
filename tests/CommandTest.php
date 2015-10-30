<?php

// Fake class
namespace SebastianBergmann\Git;

class Git
{
    protected function execute()
    {
        return [getcwd()];
    }

    public function getDiff($a, $b)
    {
        return '';
    }
}

namespace tests;

use League\CLImate\CLImate;
use phphound\Analyser;
use phphound\Command;
use ReflectionClass;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->binariesPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $commands = ['phpcs', 'phpcpd', 'phpmd'];
        foreach ($commands as $command) {
            $commandPath = $this->binariesPath . $command;
            touch($commandPath);
            chmod($commandPath, 0777);
        }
    }

    /** @test */
    public function it_prints_usage_instructions_when_receives_help_argument()
    {
        $arguments = ['php-hound', '--help'];

        $cli = $this->getMock('League\CLImate\CLImate', ['usage']);
        $cli->expects($this->once())->method('usage');

        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_prints_usage_instructions_when_receives_h_argument()
    {
        $arguments = ['php-hound', '-h'];

        $cli = $this->getMock('League\CLImate\CLImate', ['usage']);
        $cli->expects($this->once())->method('usage');

        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_prints_version_info_when_receives_version_argument()
    {
        $arguments = ['php-hound', '--version'];
        $cli = $this->getMock(
            'League\CLImate\CLImate',
            ['out', 'inline', 'red', 'green', 'yellow']
        );
        $cli->expects($this->once())->method('out')->with('PHP Hound ' . Analyser::VERSION);
        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_limit_results_by_a_git_diff()
    {
        // $arguments = ['php-hound', '--git-diff=master..branch'];
        // $cli = $this->getCliMock();
        // $command = $this->getMockBuilder(
        //         'phphound\Command',
        //         ['getAnalyser']
        //     )
        //     ->disableOriginalConstructor()
        //     ->getMock()
        // ;
        // $analyser = $this
        //     ->getMockBuilder('phphound\Analyser', ['setResultsFilter'])
        //     ->disableOriginalConstructor()
        //     ->getMock()
        // ;
        // $command
        //     ->method('getAnalyser')
        //     ->willReturn($analyser)
        // ;
        // $command
        //     ->expects($this->any())
        //     ->method('hasArgumentValue')
        //     ->with('git-diff')
        //     ->willReturn(true)
        // ;
        // $analyser
        //     ->expects($this->once())
        //     ->method('setResultsFilter')
        // ;
        // $command->run();
    }

    /** @test */
    public function it_runs_analysis_tools()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', 'src'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_relative_target_path()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', 'target/path'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR . 'target/path'], $command->getAnalysedPaths());
    }

    /** @test */
    public function it_full_target_path()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', DIRECTORY_SEPARATOR . 'target/path'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals([DIRECTORY_SEPARATOR . 'target/path'], $command->getAnalysedPaths());
    }

    /** @test */
    public function it_uses_current_dir_as_target_path_when_not_informed()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR . '.'], $command->getAnalysedPaths());
    }

    /** @test */
    public function it_accepts_ignore_param()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', '--ignore=dir'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['dir'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_accepts_ignore_param_with_multiple_directories()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', '--ignore=dir1,dir2'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['dir1', 'dir2'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_has_ignore_param_default()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['vendor', 'tests', 'features', 'spec'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_accepts_ignore_param_with_empty_value()
    {
        $cli = $this->getCliMock();
        $arguments = ['php-hound', '--ignore='];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals([], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_does_not_accept_invalid_format_value()
    {
        $this->setExpectedException('UnexpectedValueException');
        $arguments = ['php-hound', '--format=invalid'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);
    }

    /** @test */
    public function it_uses_json_output_with_format_json_param()
    {
        $arguments = ['php-hound', '--format=json'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\JsonOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_json_output_with_f_json_param()
    {
        $arguments = ['php-hound', '-f=json'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\JsonOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_xml_output_with_format_xml_param()
    {
        $arguments = ['php-hound', '--format=xml'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\XmlOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_xml_output_with_f_xml_param()
    {
        $arguments = ['php-hound', '-f=xml'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\XmlOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_csv_output_with_format_csv_param()
    {
        $arguments = ['php-hound', '--format=csv'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\CsvOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_csv_output_with_f_csv_param()
    {
        $arguments = ['php-hound', '-f=csv'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\CsvOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_html_output_with_format_html_param()
    {
        $arguments = ['php-hound', '--format=html'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\HtmlOutput', $this->invokeGetOutput($command));
    }

    /** @test */
    public function it_uses_html_output_with_f_html_param()
    {
        $arguments = ['php-hound', '-f=html'];
        $cli = $this->getCliMock();
        $command = new Command($cli, $this->binariesPath, $arguments);

        $this->assertInstanceOf('phphound\output\HtmlOutput', $this->invokeGetOutput($command));
    }

    protected function invokeGetOutput($command)
    {
        $class = new ReflectionClass($command);
        $method = $class->getMethod('getOutput');
        $method->setAccessible(true);
        return $method->invoke($command);
    }

    protected function getCliMock()
    {
        return $this->getMock(
            'League\CLImate\CLImate',
            ['out', 'inline', 'red', 'green', 'yellow', 'cyan', 'br']
        );
    }
}
