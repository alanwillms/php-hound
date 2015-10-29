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
            $this->cli->yellowFlank($fileName, '=', 2);
            foreach ($lines as $line => $issues) {
                foreach ($issues as $issue) {
                    $this->cli->cyan()->inline($line . ': ');
                    $this->cli->inline(trim($issue['message']));
                    $this->cli->br();
                }
            }
        }
    }
}
