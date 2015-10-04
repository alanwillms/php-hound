<?php
namespace phphound\output;

use phphound\Command;

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
            case Command::EVENT_STARTING_ANALYSIS:
                $this->cli->green('Starting analysis');
                break;

            case Command::EVENT_STARTING_TOOL:
                $this->cli->inline('Running ' . $message['description'] . '... ');
                $this->cli->inline('Ignored paths:');
                foreach ($message['ignoredPaths'] as $ignoredPath) {
                    $this->cli->inline(str_pad($ignoredPath, 5, ' ', STR_PAD_LEFT));
                }
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
}
