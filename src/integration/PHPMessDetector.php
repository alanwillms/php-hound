<?php
namespace phphound\integration;

/**
 * Integration of PHPHound with PHPMessDetector.
 * @see https://github.com/phpmd/phpmd
 */
class PHPMessDetector extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getCommand($targetPath)
    {
        return $this->binariesPath . 'phpmd ' . $targetPath . ' text cleancode,'
            . 'codesize,controversial,design,naming,unusedcode';
    }
}
