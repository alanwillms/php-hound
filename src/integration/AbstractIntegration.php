<?php
namespace phphound\integration;

use phphound\AnalysisResult;
use Sabre\Xml\Reader;

/**
 * Base class for integrations of PHPHound with third party analysis tools.
 */
abstract class AbstractIntegration
{
    /**
     * Directory path to where the bin scripts are located.
     * @var string directory path.
     */
    protected $binariesPath;

    /**
     * Temporary file wherein the output will be written
     * @var string temporary file path.
     */
    protected $temporaryFilePath;

    /**
     * Paths to be ignored during runtime.
     * @var array target file or directory paths to be ignored.
     */
    protected $ignoredPaths;

    /**
     * Stores binaries path.
     * @param string $binariesPath where the bin scripts are located.
     * @param string $temporaryDirPath where temporary files will be created.
     */
    public function __construct($binariesPath, $temporaryDirPath)
    {
        $this->binariesPath = $binariesPath;
        $this->temporaryFilePath = tempnam($temporaryDirPath, 'PHP-Hound');
        $this->ignoredPaths = [];
    }

    /**
     * Ignore informed targets during execution.
     * @param array $targets target file or directory paths to be ignored.
     * @return void
     */
    public function setIgnoredPaths(array $targets)
    {
        $this->ignoredPaths = $targets;
    }

    /**
     * Creates and execute tool command, returning output results.
     * @param string $targetPath file/directory path to be analysed.
     * @return string CLI JSON output.
     */
    public function run($resultSet, $targetPath)
    {
        $this->executeCommand($targetPath);
        $this->parseOutput($resultSet);
    }

    /**
     * Prepare and execute command.
     * @param string $targetPath file/directory path to be analysed.
     * @return void
     */
    protected function executeCommand($targetPath)
    {
        $command = $this->getCommand($targetPath);
        exec($command);
    }

    /**
     * Convert tool output into PHP Hound array output.
     * @return array
     */
    protected function parseOutput($resultSet)
    {
        $xml = new Reader;
        $content = $this->getOutputContent();
        if (empty($content)) {
            return [];
        }
        $xml->xml($content);
        $this->convertOutput($xml, $resultSet);
    }

    /**
     * Tool raw output.
     * @return string raw output contents.
     */
    protected function getOutputContent()
    {
        return file_get_contents($this->temporaryFilePath);
    }

    /**
     * Integration description.
     * @return string description.
     */
    abstract public function getDescription();

    /**
     * Create integration command to be run on the shell.
     * @param string $targetPath file/directory path to be analysed.
     * @return string shell command.
     */
    abstract public function getCommand($targetPath);

    /**
     * Convert integration XML output to PHP Hound format.
     * @param Reader XML reader object.
     * @param AnalysisResult result object.
     * @return void
     */
    abstract protected function convertOutput(Reader $xml, AnalysisResult $resultSet);
}
