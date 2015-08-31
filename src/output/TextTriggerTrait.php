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
     * @param mixed $data Optional message.
     * @return void
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
}
