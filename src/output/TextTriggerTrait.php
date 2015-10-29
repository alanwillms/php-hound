<?php
namespace phphound\output;

use phphound\Analyser;

/**
 * Outputs events information to the console.
 * @see TriggerableInterface
 */
trait TextTriggerTrait
{
    /**
     * Output event messages.
     * @param integer $eventType Command class event constant.
     * @param string|null $message optional message.
     * @return void
     */
    public function trigger($eventType, $message = null)
    {
        switch ($eventType) {
            case Analyser::EVENT_STARTING_ANALYSIS:
                $this->cli->green('Starting analysis');
                if (!empty($message['ignoredPaths'])) {
                    $this->cli->out('(ignored paths:)');
                    foreach ($message['ignoredPaths'] as $ignoredPath) {
                        $this->cli->red("\t" . $ignoredPath);
                    }
                }
                break;

            case Analyser::EVENT_STARTING_TOOL:
                $this->cli->inline('Running ' . $message['description'] . '... ');
                break;

            case Analyser::EVENT_FINISHED_TOOL:
                $this->cli->out('Done!');
                break;

            case Analyser::EVENT_FINISHED_ANALYSIS:
                $this->cli->br();
                $this->cli->green('Analysis complete!');
                break;
        }
    }
}
