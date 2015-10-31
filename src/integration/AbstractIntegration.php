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
     * Analysis result for this integration.
     * @var AnalysisResult result object.
     */
    protected $result;

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
        $this->result = new AnalysisResult;
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
     * Analysis results for this integration.
     * @return AnalysisResult analysis result object.
     */
    public function getAnalysisResult()
    {
        return $this->result;
    }

    /**
     * Creates and execute tool command, returning output results.
     * @param string[] $targetPaths file/directory paths to be analysed.
     * @return string CLI JSON output.
     */
    public function run($targetPaths)
    {
        $this->executeCommand($targetPaths);
        $this->processResults();
    }

    /**
     * Prepare and execute command.
     * @param string[] $targetPaths file/directory paths to be analysed.
     * @return void
     */
    protected function executeCommand($targetPaths)
    {
        exec($this->getCommand($targetPaths));
    }

    /**
     * Convert tool output into PHP Hound array output.
     * @return void
     */
    protected function processResults()
    {
        $content = $this->getOutputContent();
        if (empty($content)) {
            return;
        }
        $xml = new Reader;
        $xml->xml($content);
        $this->addIssuesFromXml($xml);
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
     * @param string[] $targetPaths file/directory paths to be analysed.
     * @return string shell command.
     */
    abstract public function getCommand($targetPaths);

    /**
     * Read issues from the XML output.
     * @param Reader $xml XML reader object.
     * @return void
     */
    abstract protected function addIssuesFromXml(Reader $xml);
}
