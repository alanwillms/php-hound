<?php
namespace phphound\output;

use phphound\AnalysisResult;

class TextOutput extends AbstractOutput implements TriggerableInterface
{
    use TextTriggerTrait;

    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        foreach ($result->toArray() as $fileName => $lines) {
            $this->cli->br();

            $issuesCount = $this->countIssues($lines);
            $issues = '(1 issue)';

            if ($issuesCount > 1) {
                $issues = '(' . $issuesCount . ' issues)';
            }

            $this->cli->yellowFlank($fileName . ' ' . $issues, '=', 2);

            foreach ($lines as $line => $issues) {
                foreach ($issues as $issue) {
                    $this->cli->cyan()->inline($line . ': ');
                    $this->cli->inline(trim($issue['message']));
                    $this->cli->br();
                }
            }
        }
    }

    /**
     * Count number of issues for a given file
     * @param array[] $file
     * @return integer number of issues in all lines
     */
    private function countIssues($file)
    {
        $counter = 0;

        foreach ($file as $line => $issues) {
            foreach ($issues as $issue) {
                $counter++;
            }
        }

        return $counter;
    }
}
