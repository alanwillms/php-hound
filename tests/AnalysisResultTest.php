<?php
namespace tests;

use phphound\AnalysisResult;

class AnalysisResultTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_adds_issues()
    {
        $anaysisResult = new AnalysisResult;
        $anaysisResult->addIssue(
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
            $anaysisResult->toArray()
        );
    }

    /** @test */
    function to_array_sorts_by_filename_and_line_number()
    {
        $anaysisResult = new AnalysisResult;
        $files = ['Z.php', 'A.php'];
        $lines = [31, 56, 11];

        foreach ($files as $file) {
            foreach ($lines as $line) {
                $anaysisResult->addIssue(
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
            $anaysisResult->toArray()
        );
    }

    /** @test */
    function it_merges_with_other_analysis_result()
    {
        $anaysisResult1 = new AnalysisResult;
        $files1 = ['Z.php', 'A.php'];
        $lines1 = [31, 56, 11];
        foreach ($files1 as $file) {
            foreach ($lines1 as $line) {
                $anaysisResult1->addIssue($file, $line, 'PHPMessDetector', 'error', 'Error');
            }
        }

        $anaysisResult2 = new AnalysisResult;
        $files2 = ['Y.php', 'B.php', 'A.php'];
        $lines2 = [300, 418];
        foreach ($files2 as $file) {
            foreach ($lines2 as $line) {
                $anaysisResult2->addIssue($file, $line, 'PHPCodeSniffer', 'error', 'Error');
            }
        }

        $anaysisResult1->mergeWith($anaysisResult2);
        $this->assertEquals(
            [
                'A.php' => [
                    11 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                    31 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                    56 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                    300 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                    418 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                ],
                'B.php' => [
                    300 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                    418 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                ],
                'Y.php' => [
                    300 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                    418 => [['tool' => 'PHPCodeSniffer', 'type' => 'error', 'message' => 'Error']],
                ],
                'Z.php' => [
                    11 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                    31 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                    56 => [['tool' => 'PHPMessDetector', 'type' => 'error', 'message' => 'Error']],
                ]
            ],
            $anaysisResult1->toArray()
        );
    }
}