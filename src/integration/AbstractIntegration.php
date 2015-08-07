<?php
namespace phphound\integration;

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
     * Stores binaries path.
     * @param string $binariesPath where the bin scripts are located.
     */
    public function __construct($binariesPath)
    {
        $this->binariesPath = $binariesPath;
    }

    /**
     * Creates and execute tool command, returning output results.
     * @param string $targetPath file/directory path to be analysed.
     * @return string CLI output.
     */
    public function run($targetPath)
    {
        return shell_exec($this->getCommand($targetPath));
    }

    /**
     * Create integration command to be run on the shell.
     * @param string $targetPath file/directory path to be analysed.
     * @return string shell command.
     */
    abstract public function getCommand($targetPath);
}
