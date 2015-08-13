<?php
namespace phphound\output;

use phphound\AnalysisResult;
use phphound\Command;

class TextOutput extends AbstractOutput
{
    /**
     * @inheritdoc
     */
    public function trigger($eventType, $data = null)
    {
        switch ($eventType) {
            case Command::EVENT_STARTING_ANALYSIS:
                $this->cli->green('Starting analysis');
                break;

            case Command::EVENT_STARTING_TOOL:
                $this->cli->inline('Running ' . $data . '... ');
                break;

            case Command::EVENT_FINISHED_TOOL:
                $this->cli->out('Done!');
                break;

            case Command::EVENT_FINISHED_ANALYSIS:
                $this->cli->br();
                $this->cli->green('Analysis complete!');
                break;
        }
    }

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
                    $this->cli->cyanInline($line . ': ');
                    $this->cli->inline(trim($issue['message']));
                    $this->cli->br();
                }
            }
        }
    }
}
