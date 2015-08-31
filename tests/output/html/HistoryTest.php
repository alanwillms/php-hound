<?php
namespace tests\output;

use phphound\output\html\History;

class HistoryTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        $historyFile = sys_get_temp_dir() . '/history.json';
        if (file_exists($historyFile)) {
            unlink($historyFile);
        }
    }

    /** @test */
    function it_appends_results_data()
    {
        $directory = sys_get_temp_dir();
        $data = [
            'File.php' => [
                93 => [
                    ['tool' => 'PHPMessDetector', 'type' => 'xxx', 'message' => 'xxx'],
                    ['tool' => 'PHPCodeSniffer', 'type' => 'xxx', 'message' => 'xxx'],
                ],
                111 => [
                    ['tool' => 'PHPCPD', 'type' => 'xxx', 'message' => 'xxx'],
                ],
            ],
        ];
        $result = $this->getMock('phphound\AnalysisResult');
        $history = new History($directory);
        $result->expects($this->once())->method('toArray')->willReturn($data);
        $history->append($result);
        $data = $history->getData();

        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('executions', $data);
        $this->assertArrayHasKey('historyData', $data);
        $this->assertEquals(1, count($data['executions']));
        $this->assertEquals(
            [
                'PHPMessDetector' => ['name' => 'PHPMessDetector', 'data' => [1]],
                'PHPCodeSniffer' => ['name' => 'PHPCodeSniffer', 'data' => [1]],
                'PHPCPD' => ['name' => 'PHPCPD', 'data' => [1]],
            ],
            $data['historyData']
        );
    }

    /** @test */
    function it_loads_existing_data()
    {
        $directory = sys_get_temp_dir();
        $data = [
            'executions' => ['Aug 31 12:30'],
            'historyData' => [
                'PHPMessDetector' => ['name' => 'PHPMessDetector', 'data' => [1]],
                'PHPCodeSniffer' => ['name' => 'PHPCodeSniffer', 'data' => [1]],
                'PHPCPD' => ['name' => 'PHPCPD', 'data' => [1]],
            ]
        ];
        file_put_contents($directory . '/history.json', json_encode($data));
        $history = new History($directory);
        $this->assertEquals($data, $history->getData());
    }

    /** @test */
    function it_saves_data()
    {
        $directory = sys_get_temp_dir();
        $data = [
            'File.php' => [
                93 => [
                    ['tool' => 'PHPMessDetector', 'type' => 'xxx', 'message' => 'xxx'],
                    ['tool' => 'PHPCodeSniffer', 'type' => 'xxx', 'message' => 'xxx'],
                ],
                111 => [
                    ['tool' => 'PHPCPD', 'type' => 'xxx', 'message' => 'xxx'],
                ],
            ],
        ];
        $history = new History($directory);
        $result = $this->getMock('phphound\AnalysisResult');
        $result->expects($this->once())->method('toArray')->willReturn($data);
        $history->append($result);
        $history->save();

        $this->assertFileExists($directory . '/history.json');
        $fileContent = file_get_contents($directory . '/history.json');
        $this->assertEquals(json_encode($history->getData()), $fileContent);
    }

    /** @test */
    function it_doesnot_break_with_a_new_integration_setup_after_a_few_runs()
    {
        $directory = sys_get_temp_dir();
        $oldHistory = [
            'executions' => ['Aug 31 12:30', 'Sep 2 15:17'],
            'historyData' => [
                'PHPMessDetector' => ['name' => 'PHPMessDetector', 'data' => [12, 3]],
                'PHPCodeSniffer' => ['name' => 'PHPCodeSniffer', 'data' => [5, 2]],
                'PHPCopyPasteDetector' => ['name' => 'PHPCopyPasteDetector', 'data' => [1, 1]],
            ]
        ];
        $data = [
            'File.php' => [
                111 => [
                    ['tool' => 'PHPMessDetector', 'type' => 'xxx', 'message' => 'xxx'],
                    ['tool' => 'PHPCodeSniffer', 'type' => 'xxx', 'message' => 'xxx'],
                    ['tool' => 'PHPCopyPasteDetector', 'type' => 'xxx', 'message' => 'xxx'],
                    ['tool' => 'NewTool', 'type' => 'xxx', 'message' => 'xxx'],
                ],
            ],
        ];
        $newHistory = [
            'executions' => ['Aug 31 12:30', 'Sep 2 15:17', 'Sep 7 23:45'],
            'historyData' => [
                'PHPMessDetector' => ['name' => 'PHPMessDetector', 'data' => [12, 3, 1]],
                'PHPCodeSniffer' => ['name' => 'PHPCodeSniffer', 'data' => [5, 2, 1]],
                'PHPCopyPasteDetector' => ['name' => 'PHPCopyPasteDetector', 'data' => [1, 1, 1]],
                'NewTool' => ['name' => 'NewTool', 'data' => [0, 0, 1]],
            ]
        ];

        file_put_contents($directory . '/history.json', json_encode($oldHistory));

        $history = new History($directory);
        $result = $this->getMock('phphound\AnalysisResult');
        $result->expects($this->once())->method('toArray')->willReturn($data);
        $history->append($result);

        $historyData = $history->getData();

        $this->assertEquals($newHistory['historyData'], $historyData['historyData']);
    }
}
