<?php
namespace phphound\output;

use League\Csv\Writer;
use phphound\AnalysisResult;

class CsvOutput extends AbstractOutput
{
    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        $writer = Writer::createFromString('');
        $writer->insertOne(['File', 'Line', 'Tool', 'Type', 'Message']);

        foreach ($result->toArray() as $fileName => $lines) {
            foreach ($lines as $lineNumber => $issues) {
                foreach ($issues as $issue) {
                    $writer->insertOne([
                        $fileName,
                        $lineNumber,
                        $issue['tool'],
                        $issue['type'],
                        trim($issue['message']),
                    ]);
                }
            }
        }

        $csv = ltrim($writer->__toString());
        $this->cli->out($csv);
    }
}
