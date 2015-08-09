<?php
namespace tests;

use phphound\Command;

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
    public function it_prints_usage_when_get_help_argument()
    {
        $arguments = ['php-hound', '--help'];

        $cli = $this->getMock('League\CLImate\CLImate', ['usage']);
        $cli->expects($this->once())->method('usage');

        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_runs_analysis_tools()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound', 'src'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $command->run();
    }

    /** @test */
    public function it_accepts_target_path()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound', 'target/path'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals('target/path', $command->getAnalysedPath());
    }

    /** @test */
    public function it_uses_current_dir_as_target_path_when_not_informed()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound'];
        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals('.', $command->getAnalysedPath());
    }

    /** @test */
    public function it_accepts_ignore_param()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound', '--ignore=dir'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['dir'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_accepts_ignore_param_with_multiple_directories()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound', '--ignore=dir1,dir2'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['dir1', 'dir2'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_has_ignore_param_default()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound'];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals(['vendor', 'tests', 'features', 'spec'], $command->getIgnoredPaths());
    }

    /** @test */
    public function it_accepts_ignore_param_with_empty_value()
    {
        $cli = $this->getMock('League\CLImate\CLImate', ['output']);
        $arguments = ['php-hound', '--ignore='];

        $command = new Command($cli, $this->binariesPath, $arguments);
        $this->assertEquals([], $command->getIgnoredPaths());
    }
}
