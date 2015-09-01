<?php
namespace phphound;

use phphound\output\AbstractOutput;
use phphound\output\TriggerableInterface;
use UnexpectedValueException;
use League\CLImate\CLImate;

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
     * @var AbstractOutput output instance.
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
        if ($this->hasArgumentValue('help')) {
            $this->cli->usage();
            return null;
        }

        if ($this->hasArgumentValue('version')) {
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
     * @throws UnexpectedValueException on invalid format value.
     * @return void
     */
    protected function initializeOutput()
    {
        $format = $this->getOutputFormat();
        $formatClasses = $this->getOutputFormatClasses();

        if (!isset($formatClasses[$format])) {
            throw new UnexpectedValueException(
                'Invalid format: "' . $format . '"'
            );
        }

        $outputClassName = $formatClasses[$format];

        $this->output = new $outputClassName(
            $this->cli,
            $this->getWorkingDirectory()
        );
    }

    /**
     * Run each configured PHP analysis tool.
     * @return void
     */
    protected function runAllAnalysisTools()
    {
        $result = new AnalysisResult;
        $this->trigger(self::EVENT_STARTING_ANALYSIS);
        foreach ($this->getAnalysisTools() as $tool) {
            $this->trigger(self::EVENT_STARTING_TOOL, $tool->getDescription());
            $tool->run($this->getAnalysedPath());
            $result->mergeWith($tool->getAnalysisResult());
            $this->trigger(self::EVENT_FINISHED_TOOL);
        }
        $this->output->result($result);
        $this->trigger(self::EVENT_FINISHED_ANALYSIS);
    }

    /**
     * Call an output trigger if supported.
     * @param int $event occurred event.
     * @param string|null $message optional message.
     * @return void
     */
    protected function trigger($event, $message = null)
    {
        if ($this->output instanceof TriggerableInterface) {
            $this->output->trigger($event, $message);
        }
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
        return '0.6.0';
    }

    /**
     * Command line arguments list for CLImate.
     * @return array CLI list of arguments.
     */
    protected function getArguments()
    {
        return [
            'help' => [
                'prefix' => 'h',
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
            'format' => [
                'prefix' => 'f',
                'longPrefix' => 'format',
                'description' => 'Output format',
                'castTo' => 'string',
                'defaultValue' => 'text',
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
        $ignoredArgument = $this->getArgumentValue('ignore');
        $ignoredPaths = explode(',', $ignoredArgument);
        return array_filter($ignoredPaths);
    }

    /**
     * Analysis target path.
     * @return string target path.
     */
    public function getAnalysedPath()
    {
        return $this->getArgumentValue('path');
    }

    /**
     * Running script path.
     * @return string current script directory.
     */
    public function getWorkingDirectory()
    {
        return getcwd();
    }

    /**
     * Output format.
     * @return string format type.
     */
    public function getOutputFormat()
    {
        return $this->getArgumentValue('format');
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

    /**
     * Set of PHP analys integration objects.
     * @return phphound\integration\AbstractIntegration[] set of objects.
     */
    protected function getAnalysisTools()
    {
        $objects = [];

        foreach ($this->getAnalysisToolsClasses() as $className) {
            $tool = new $className($this->binariesPath, sys_get_temp_dir());
            $tool->setIgnoredPaths($this->getIgnoredPaths());
            $objects[] = $tool;
        }

        return $objects;
    }

    /**
     * List of output format classes.
     * @return array array where the key is a format and its value the class.
     */
    protected function getOutputFormatClasses()
    {
        return [
            'text' => 'phphound\output\TextOutput',
            'json' => 'phphound\output\JsonOutput',
            'xml' => 'phphound\output\XmlOutput',
            'csv' => 'phphound\output\CsvOutput',
            'html' => 'phphound\output\HtmlOutput',
        ];
    }

    /**
     * Get argument value from user informed arguments.
     * @param string $name argument name.
     * @return Mixed argument value.
     */
    protected function getArgumentValue($name)
    {
        return $this->cli->arguments->get($name);
    }

    /**
     * Check if the user supplied an argument.
     * @param string $name argument name.
     * @return boolean if the argument has informed or not.
     */
    protected function hasArgumentValue($name)
    {
        return $this->cli->arguments->defined($name, $this->arguments);
    }
}
