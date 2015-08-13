<?php
namespace phphound\output;

use phphound\AnalysisResult;
use Sabre\Xml\Writer;

class XmlOutput extends AbstractOutput
{
    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        $this->cli->out($this->getXmlFor($result));
    }

    /**
     * Prepare XML file based on AnalysisResult.
     * @param AnalysisResult $result analysis result object.
     * @return string XML contents.
     */
    protected function getXmlFor(AnalysisResult $result)
    {
        $writer = new Writer;
        $writer->openMemory();
        $writer->write($this->getSabreXmlArrayFor($result));
        return '<?xml version="1.0" encoding="UTF-8"?>' . $writer->outputMemory();
    }

    /**
     * Prepare XML array for Sabre XML Writer.
     * @param AnalysisResult $result analysis result object.
     * @return array XML following Sabre structure.
     */
    protected function getSabreXmlArrayFor(AnalysisResult $result)
    {
        $sabreXmlArray = [
            'phphound' => [],
        ];

        foreach ($result->toArray() as $fileName => $lines) {
            $linesForXml = [];

            foreach ($lines as $lineNumber => $issues) {
                $issuesForXml = [];

                foreach ($issues as $issue) {
                    $issuesForXml[] = [
                        'name' => 'issue',
                        'value' => trim($issue['message']),
                        'attributes' => [
                            'tool' => $issue['tool'],
                            'type' => $issue['type'],
                        ]
                    ];
                }

                $linesForXml[] = [
                    'name' => 'line',
                    'value' => $issuesForXml,
                    'attributes' => ['number' => $lineNumber],
                ];
            }

            $sabreXmlArray['phphound'][] = [
                'name' => 'file',
                'value' => $linesForXml,
                'attributes' => ['name' => $fileName],
            ];
        }

        return $sabreXmlArray;
    }
}
