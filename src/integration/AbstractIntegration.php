<?php
namespace phphound\integration;

use Sabre\Xml\Reader;

/**
 * Base class for integrations of PHPHound with third party analysis tools.
 */
abstract class AbstractIntegration
{
    /**
     * Directory path to where the bin scripts are located.
     * @var strind directory path.
     */
    protected $binariesPath;

    /**
     * Temporary file wherein the output will be written
     * @var strind temporary file path.
     */
    protected $temporaryFilePath;

    /**
     * Stores binaries path.
     * @param string $binariesPath where the bin scripts are located.
     * @param string $temporaryDirectoryPath where temporary files will be created.
     */
    public function __construct($binariesPath, $temporaryDirectoryPath)
    {
        $this->binariesPath = $binariesPath;
        $this->temporaryFilePath = tempnam($temporaryDirectoryPath, 'PHP-Hound');
    }

    /**
     * Creates and execute tool command, returning output results.
     * @param string $targetPath file/directory path to be analysed.
     * @return string CLI JSON output.
     */
    public function run($targetPath)
    {
        $this->executeCommand($targetPath);
        return json_encode($this->parseOutput());
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
    protected function parseOutput()
    {
        $xml = new Reader;
        $content = $this->getOutputContent();
        if (empty($content)) {
            return [];
        }
        $xml->xml($content);
        return $this->convertOutput($xml);
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
     * Create integration command to be run on the shell.
     * @param string $targetPath file/directory path to be analysed.
     * @return string shell command.
     */
    abstract public function getCommand($targetPath);

    /**
     * Convert integration XML output to PHP Hound format.
     * @param Reader XML reader object.
     * @return array PHP Hound output.
     * <code>
     * return [
     *     "/path/to/file1.php" => [
     *         1 => [
     *             [
     *                 'tool' => 'IntegrationName',
     *                 'type' => 'integration.issue.type',
     *                 'message' => 'Missing whitespace after argument.'
     *             ]
     *         ]
     *     ],
     *     "/path/to/file2.php" => [
     *         36 => [
     *             [
     *                 'tool' => 'IntegrationName',
     *                 'type' => 'integration.issue.type',
     *                 'message' => 'Missing whitespace after argument.'
     *             ]
     *         ]
     *     ],
     * ];
     * </code>
     */
    abstract protected function convertOutput(Reader $xml);
}
