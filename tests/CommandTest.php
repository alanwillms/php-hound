<?php
use phphound\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->binariesPath = sys_get_temp_dir() . PATH_SEPARATOR;
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
}
