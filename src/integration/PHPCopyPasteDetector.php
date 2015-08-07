<?php
namespace phphound\integration;

use Sabre\Xml\Reader;
use phphound\helper\ArrayHelper;

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
        return $this->binariesPath . 'phpcpd '. $targetPath . ' --log-pmd="'
            . $this->temporaryFilePath . '"';
    }

    /**
     * @inheritdoc
     */
    protected function convertOutput(Reader $xml)
    {
        $xmlArray = $xml->parse();
        $files = [];

        foreach (ArrayHelper::ensure($xmlArray['value']) as $duplicationTag) {
            if ($duplicationTag['name'] != '{}duplication'
                || empty($duplicationTag['value'])) {
                continue;
            }

            foreach (ArrayHelper::ensure($duplicationTag['value']) as $fileTag) {
                if ($fileTag['name'] != '{}file') {
                    continue;
                }

                $fileName = $fileTag['attributes']['path'];
                $line = $fileTag['attributes']['line'];
                $message = 'Duplicated code';

                if (!isset($files[$fileName])) {
                    $files[$fileName] = [];
                }

                if (!isset($files[$fileName][$line])) {
                    $files[$fileName][$line] = [];
                }

                $files[$fileName][$line] = [
                    'tool' => 'PHPCopyPasteDetector',
                    'type' => 'duplication',
                    'message' => 'Duplicated code',
                ];
            }
        }

        return $files;
    }
}
