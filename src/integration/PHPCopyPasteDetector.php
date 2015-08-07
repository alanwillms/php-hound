<?php
namespace phphound\integration;

/**
 * Integration of PHPHound with PHPCopyPasteDetector.
 * @see https://github.com/sebastianbergmann/phpcpd
 */
class PHPCopyPasteDetector extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getCommand($targetPath)
    {
        return $this->binariesPath . 'phpcpd '. $targetPath;
    }
}
