<?php
namespace tests\integration;

use phphound\integration\PHPCopyPasteDetector;

class PHPCopyPasteDetectorTest extends \PHPUnit_Framework_TestCase
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
        $integration = new PHPCopyPasteDetector($this->binariesPath);
        $this->assertEquals(
            $this->binariesPath . 'phpcpd target.php',
            $integration->getCommand('target.php')
        );
    }
}
