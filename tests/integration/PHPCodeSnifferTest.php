<?php
namespace tests\integration;

use phphound\AnalysisResult;
use phphound\integration\PHPCodeSniffer;

class PHPCodeSnifferTest extends \PHPUnit_Framework_TestCase
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
    public function it_gets_command()
    {
        $integration = new PHPCodeSniffer($this->binariesPath, $this->binariesPath);
        $this->assertContains(
            'phpcs -p --standard=PSR2 --report=xml --report-file=',
            $integration->getCommand('target.php')
        );
    }

    /** @test */
    public function it_respects_ignore_param()
    {
        $integration = new PHPCodeSniffer($this->binariesPath, $this->binariesPath);
        $integration->setIgnoredPaths(['dir1', 'dir2']);
        $this->assertContains(
            '--ignore=dir1,dir2',
            $integration->getCommand('target.php')
        );
    }

    /** @test */
    public function it_correctly_parses_xml_data()
    {
        $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<phpcs version="1.5.6">
<invalid tag="here" />
<file name="User.php" errors="1" warnings="0">
 <error line="75" column="45" source="EndLine" severity="5">Whitespace found at end of line</error>
 <warning line="32" column="138" source="TooLong" severity="5">Line exceeds 120 characters</warning>
</file>
<file name="Category.php" errors="0" warnings="1">
 <warning line="33" column="139" source="TooLong" severity="5">Line exceeds 120 characters</warning>
</file>
</phpcs>
EOT;
        $integration = $this->getMock(
            'phphound\integration\PHPCodeSniffer',
            ['getOutputContent'],
            [$this->binariesPath, $this->binariesPath]
        );
        $integration->expects($this->any())->method('getOutputContent')->willReturn($xml);
        $integration->run('target.php');

        $this->assertEquals(
            [
                'Category.php' => [
                    33 => [['tool' => 'PHPCodeSniffer', 'type' => 'TooLong', 'message' => 'Line exceeds 120 characters']],
                ],
                'User.php' => [
                    32 => [['tool' => 'PHPCodeSniffer', 'type' => 'TooLong', 'message' => 'Line exceeds 120 characters']],
                    75 => [['tool' => 'PHPCodeSniffer', 'type' => 'EndLine', 'message' => 'Whitespace found at end of line']],
                ]
            ],
            $integration->getAnalysisResult()->toArray()
        );
    }
}
