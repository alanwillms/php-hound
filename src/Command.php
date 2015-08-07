<?php
namespace phphound;

use League\CLImate\CLImate;
use phphound\integration\PHPCodeSniffer;
use phphound\integration\PHPCopyPasteDetector;
use phphound\integration\PHPMessDetector;

/**
 * Command line tool that run all script analyzers.
 */
class Command
{
    /**
     * Composer binaries path.
     * @var string directory path.
     */
    protected $binariesPath;

    /**
     * CLI tool.
     * @var CLImate CLImate instance.
     */
    protected $cli;

    /**
     * Command line arguments.
     * @var array list of arguments.
     */
    protected $arguments;

    /**
     * Set dependencies and initialize CLI.
     * @param CLImate $climate CLImate instance.
     * @param string $binariesPath Composer binaries path.
     * @param array $arguments command line arguments.
     */
    public function __construct(CLImate $climate, $binariesPath, array $arguments)
    {
        $this->cli = $climate;
        $this->binariesPath = $binariesPath;
        $this->arguments = $arguments;

        $this->initialize();
    }

    /**
     * Run PHP-Hound command.
     * @return null
     */
    public function run()
    {
        if ($this->cli->arguments->defined('help', $this->arguments)) {
            $this->cli->usage();
            return null;
        }
        $this->runAllAnalysisTools();
    }

    /**
     * Initialize CLI tool.
     * @return void
     */
    protected function initialize()
    {
        $this->cli->description($this->getDescription());
        $this->cli->arguments->add($this->getArguments());
        $this->cli->arguments->parse($this->arguments);
    }

    /**
     * Run each configured PHP analysis tool.
     * @return void
     */
    protected function runAllAnalysisTools()
    {
        foreach ($this->getAnalysisToolsCommands() as $command) {
            $result = $command->run($this->getAnalysedPath());
            $this->cli->output($result);
        }
    }

    /**
     * CLI output description.
     * @return string description.
     */
    protected function getDescription()
    {
        return 'PHP Hound 0.1';
    }

    /**
     * Command line arguments list for CLImate.
     * @return array CLI list of arguments.
     */
    protected function getArguments()
    {
        return [
            'help' => [
                'longPrefix' => 'help',
                'description' => 'Prints a usage statement',
                'noValue' => true,
            ],
            'path' => [
                'description' => 'File or directory path to analyze',
            ],
        ];
    }

    /**
     * Analysis target path.
     * @return string target path.
     */
    protected function getAnalysedPath()
    {
        return $this->cli->arguments->get('path');
    }

    /**
     * List of PHP analys commands.
     * @return array each command with its arguments.
     */
    protected function getAnalysisToolsCommands()
    {
        return [
            new PHPCodeSniffer($this->binariesPath),
            new PHPCopyPasteDetector($this->binariesPath),
            new PHPMessDetector($this->binariesPath),
        ];
    }
}
