<?php
namespace tests\integration;

use phphound\AnalysisResult;
use phphound\integration\PHPCopyPasteDetector;

class PHPCopyPasteDetectorTest extends \PHPUnit_Framework_TestCase
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
        $integration = new PHPCopyPasteDetector($this->binariesPath, $this->binariesPath);
        $this->assertContains(
            'phpcpd target.php',
            $integration->getCommand('target.php')
        );
    }

    /** @test */
    public function it_respects_ignore_param()
    {
        $integration = new PHPCopyPasteDetector($this->binariesPath, $this->binariesPath);
        $integration->setIgnoredPaths(['dir1', 'dir2']);
        $this->assertContains(
            '--exclude={dir1,dir2}',
            $integration->getCommand('target.php')
        );
    }

    /** @test */
    public function it_correctly_parses_xml_data()
    {
        $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<pmd-cpd>
  <invalid tag="here" />
  <duplication lines="19" tokens="84">
    <file path="ClassMirrorSpec.php" line="34"/>
    <file path="ClassMirrorSpec.php" line="394"/>
    <codefragment>duplicated code here</codefragment>
  </duplication>
</pmd-cpd>
EOT;
        $integration = $this->getMock(
            'phphound\integration\PHPCopyPasteDetector',
            ['getOutputContent'],
            [$this->binariesPath, $this->binariesPath]
        );
        $integration->expects($this->any())->method('getOutputContent')->willReturn($xml);
        $resultSet = new AnalysisResult;
        $integration->run($resultSet, 'target.php');

        $this->assertEquals(
            [
                'ClassMirrorSpec.php' => [
                    34 => [['tool' => 'PHPCopyPasteDetector', 'type' => 'duplication', 'message' => 'Duplicated code']],
                    394 => [['tool' => 'PHPCopyPasteDetector', 'type' => 'duplication', 'message' => 'Duplicated code']],
                ],
            ],
            $resultSet->toArray()
        );
    }
}
