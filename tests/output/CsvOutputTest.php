<?php
namespace tests\output;

use phphound\Command;
use phphound\output\CsvOutput;

class CsvOutputTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_outputs_results_correctly()
    {
        $data = [
            'File.php' => [
                93 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => '   Error trimmed  ']],
            ],
        ];
        $csv = implode("\n", [
            'File,Line,Tool,Type,Message',
            'File.php,93,PHP-Hound,error,"Error trimmed"',
            '',
        ]);

        $cli = $this->getMockBuilder('League\CLImate\CLImate')
            ->setMethods(['out'])
            ->getMock()
        ;
        $result = $this->getMock('phphound\AnalysisResult');
        $output = new CsvOutput($cli, sys_get_temp_dir());

        $result->expects($this->once())->method('toArray')->willReturn($data);
        $cli->expects($this->once())->method('out')->with($csv);

        $output->result($result);
    }
}