<?php
namespace phphound\output;

use League\CLImate\CLImate;
use phphound\AnalysisResult;
use phphound\Command;

abstract class AbstractOutput
{
    /**
     * CLI tool.
     * @var CLImate CLImate instance.
     */
    protected $cli;

    /**
     * Set dependencies.
     * @param CLImate $climate CLImate instance.
     */
    public function __construct(CLImate $climate)
    {
        $this->cli = $climate;
    }

    /**
     * Log event messages.
     * @param integer $eventType Command class event constant.
     * @param mixed $data Optional message.
     * @return void
     */
    public function trigger($eventType, $data = null)
    {
    }

    /**
     * Outputs reduced analysis result.
     * @param  AnalysisResult $result reduced result data.
     * @return void
     */
    abstract public function result(AnalysisResult $result);
}
