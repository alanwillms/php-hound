<?php
namespace phphound;

use League\CLImate\CLImate;
use phphound\output\AbstractOutput;
use UnexpectedValueException;

/**
 * Command line tool that run all script analyzers.
 */
class Command
{
    /**
     * CLI tool.
     * @var CLImate CLImate instance.
     */
    protected $cli;

    /**
     * Analyser.
     * @var Analyser analyser instance.
     */
    protected $analyser;

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
        $this->arguments = $arguments;

        $this->cli->arguments->add($this->getArguments());
        $this->cli->arguments->parse($this->arguments);

        $this->analyser = new Analyser(
            $this->getOutput(),
            $binariesPath,
            $this->getAnalysedPath(),
            $this->getIgnoredPaths()
        );
        $this->cli->description($this->analyser->getDescription());
    }

    /**
     * Run PHP-Hound command.
     * @return null
     */
    public function run()
    {
        if ($this->hasArgumentValue('help')) {
            $this->cli->usage();
            return;
        }

        if ($this->hasArgumentValue('version')) {
            $this->cli->out($this->analyser->getDescription());
            return;
        }

        $this->analyser->run();
    }

    /**
     * Initialize output.
     * @throws UnexpectedValueException on invalid format value.
     * @return AbstractOutput
     */
    protected function getOutput()
    {
        $format = $this->getOutputFormat();
        $formatClasses = $this->getOutputFormatClasses();

        if (!isset($formatClasses[$format])) {
            throw new UnexpectedValueException(
                'Invalid format: "' . $format . '"'
            );
        }

        $outputClassName = $formatClasses[$format];

        return new $outputClassName(
            $this->cli,
            $this->getWorkingDirectory()
        );
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
