<?php
namespace phphound;

use League\CLImate\CLImate;
use phphound\output\TextOutput;

/**
 * Command line tool that run all script analyzers.
 */
class Command
{
    const EVENT_STARTING_ANALYSIS = 0;
    const EVENT_STARTING_TOOL = 1;
    const EVENT_FINISHED_TOOL = 2;
    const EVENT_FINISHED_ANALYSIS = 3;

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
     * Output service.
     * @var TextOutput TextOutput instance.
     */
    protected $output;

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

        $this->initializeCLI();
        $this->initializeOutput();
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

        if ($this->cli->arguments->defined('version', $this->arguments)) {
            $this->cli->out($this->getDescription());
            return null;
        }

        $this->runAllAnalysisTools();
    }

    /**
     * Initialize CLI tool.
     * @return void
     */
    protected function initializeCLI()
    {
        $this->cli->description($this->getDescription());
        $this->cli->arguments->add($this->getArguments());
        $this->cli->arguments->parse($this->arguments);
    }

    /**
     * Initialize output.
     * @return void
     */
    protected function initializeOutput()
    {
        $this->output = new TextOutput($this->cli);
    }

    /**
     * Run each configured PHP analysis tool.
     * @return void
     */
    protected function runAllAnalysisTools()
    {
        $this->output->trigger(self::EVENT_STARTING_ANALYSIS);
        $resultSet = new AnalysisResult;
        foreach ($this->getAnalysisToolsClasses() as $className) {
            $command = new $className($this->binariesPath, sys_get_temp_dir());
            $command->setIgnoredPaths($this->getIgnoredPaths());
            $this->output->trigger(self::EVENT_STARTING_TOOL, $command->getDescription());
            $command->run($resultSet, $this->getAnalysedPath());
            $this->output->trigger(self::EVENT_FINISHED_TOOL);
        }
        $this->output->result($resultSet);
        $this->output->trigger(self::EVENT_FINISHED_ANALYSIS);
    }

    /**
     * CLI output description.
     * @return string description.
     */
    protected function getDescription()
    {
        return 'PHP Hound ' . $this->getVersion();
    }

    /**
     * Current PHP Hound version.
     * @return string semantic version.
     */
    protected function getVersion()
    {
        return '0.4.0';
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
            'version' => [
                'prefix' => 'v',
                'longPrefix' => 'version',
                'description' => 'Prints installed version',
                'noValue' => true,
            ],
            'ignore' => [
                'prefix' => 'i',
                'longPrefix' => 'ignore',
                'description' => 'Ignore a comma-separated list of directories',
                'castTo' => 'string',
                'defaultValue' => 'vendor,tests,features,spec',
            ],
            'path' => [
                'description' => 'File or directory path to analyze',
                'defaultValue' => '.',
            ],
        ];
    }

    /**
     * Get a list of paths to be ignored by the analysis.
     * @return array a list of file and/or directory paths.
     */
    public function getIgnoredPaths()
    {
        $ignoredArgument = $this->cli->arguments->get('ignore', $this->arguments);
        $ignoredPaths = explode(',', $ignoredArgument);
        return array_filter($ignoredPaths);
    }

    /**
     * Analysis target path.
     * @return string target path.
     */
    public function getAnalysedPath()
    {
        return $this->cli->arguments->get('path', $this->arguments);
    }

    /**
     * List of PHP analys integration classes.
     * @return array array of class names.
     */
    protected function getAnalysisToolsClasses()
    {
        return [
            'phphound\integration\PHPCodeSniffer',
            'phphound\integration\PHPCopyPasteDetector',
            'phphound\integration\PHPMessDetector',
        ];
    }
}
