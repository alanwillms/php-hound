<?php
namespace phphound\integration;

/**
 * Integration of PHPHound with PHPCodeSniffer.
 * @see https://github.com/squizlabs/PHP_CodeSniffer
 */
class PHPCodeSniffer extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getCommand($targetPath)
    {
        return $this->binariesPath . 'phpcs -p --extensions=php --standard=PSR2 '. $targetPath;
    }
}
