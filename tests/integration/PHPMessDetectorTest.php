<?php
namespace tests\integration;

use phphound\AnalysisResult;
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
        $integration = new PHPMessDetector($this->binariesPath, $this->binariesPath);
        $this->assertContains(
            'phpmd target.php xml cleancode,codesize,controversial,design,naming,unusedcode',
            $integration->getCommand('target.php')
        );
    }

    /** @test */
    public function it_correctly_parses_xml_data()
    {
        $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8" ?>
<pmd version="1.5.0" timestamp="2015-08-07T18:13:28-03:00">
  <invalid tag="here" />
  <file name="LoginAttempt.php">
    <violation beginline="31" endline="31" rule="StaticAccess" ruleset="Clean Code Rules" externalInfoUrl="http://phpmd.org/rules/design.html#staticaccess" priority="1">Avoid using static access to class self</violation>
    <violation beginline="44" endline="44" rule="ShortVariable" ruleset="Naming Rules" externalInfoUrl="http://phpmd.org/rules/naming.html#shortvariable" priority="3">Avoid variables with short names</violation>
  </file>
  <file name="PermalinksController.php">
    <violation beginline="10" endline="10" rule="ShortVariable" ruleset="Naming Rules" externalInfoUrl="http://phpmd.org/rules/naming.html#shortvariable" priority="3">Avoid variables with short names</violation>
  </file>
</pmd>
EOT;
        $integration = $this->getMock(
            'phphound\integration\PHPMessDetector',
            ['getOutputContent'],
            [$this->binariesPath, $this->binariesPath]
        );
        $integration->expects($this->any())->method('getOutputContent')->willReturn($xml);
        $resultSet = new AnalysisResult;
        $integration->run($resultSet, 'target.php');

        $this->assertEquals(
            [
                'LoginAttempt.php' => [
                    31 => [['tool' => 'PHPMessDetector', 'type' => 'StaticAccess', 'message' => 'Avoid using static access to class self']],
                    44 => [['tool' => 'PHPMessDetector', 'type' => 'ShortVariable', 'message' => 'Avoid variables with short names']],
                ],
                'PermalinksController.php' => [
                    10 => [['tool' => 'PHPMessDetector', 'type' => 'ShortVariable', 'message' => 'Avoid variables with short names']],
                ]
            ],
            $resultSet->toArray()
        );
    }
}
