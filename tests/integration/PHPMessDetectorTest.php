<?php
namespace tests\integration;

use phphound\integration\PHPMessDetector;

class PHPMessDetectorTest extends \PHPUnit_Framework_TestCase
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
    public function it_gets_command()
    {
        $integration = new PHPMessDetector($this->binariesPath);
        $this->assertEquals(
            $this->binariesPath . 'phpmd target.php text cleancode,codesize,controversial,design,naming,unusedcode',
            $integration->getCommand('target.php')
        );
    }
}
