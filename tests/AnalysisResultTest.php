<?php
namespace tests;

use phphound\AnalysisResult;

class AnalysisResultTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_adds_issues()
    {
        $resultSet = new AnalysisResult;
        $resultSet->addIssue(
            'Class.php',
            123,
            'PHP-Hound',
            'ugly_error',
            'Ugly error'
        );
        $this->assertEquals(
            [
                'Class.php' => [
                    123 => [
                        [
                            'tool' => 'PHP-Hound',
                            'type' => 'ugly_error',
                            'message' => 'Ugly error',
                        ]
                    ]
                ]
            ],
            $resultSet->toArray()
        );
    }

    /** @test */
    public function to_array_sorts_by_filename_and_line_number()
    {
        $resultSet = new AnalysisResult;
        $files = ['Z.php', 'A.php'];
        $lines = [31, 56, 11];

        foreach ($files as $file) {
            foreach ($lines as $line) {
                $resultSet->addIssue(
                    $file,
                    $line,
                    'PHP-Hound',
                    'error',
                    'Error'
                );
            }
        }

        $this->assertEquals(
            [
                'A.php' => [
                    11 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                    31 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                    56 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                ],
                'Z.php' => [
                    11 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                    31 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                    56 => [['tool' => 'PHP-Hound', 'type' => 'error', 'message' => 'Error']],
                ]
            ],
            $resultSet->toArray()
        );
    }
}