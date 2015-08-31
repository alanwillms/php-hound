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
    public function it_appends_results_data()
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
    public function it_loads_existing_data()
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
    public function it_saves_data()
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
        $history->save();

        $this->assertFileExists($directory . '/history.json');
        $fileContent = file_get_contents($directory . '/history.json');
        $this->assertEquals(json_encode($history->getData()), $fileContent);
    }
}
