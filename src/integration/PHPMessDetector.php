<?php
namespace phphound\integration;

use Sabre\Xml\Reader;
use phphound\helper\ArrayHelper;

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
        return $this->binariesPath . 'phpmd ' . $targetPath . ' xml cleancode,'
            . 'codesize,controversial,design,naming,unusedcode > "'
            . $this->temporaryFilePath . '"';
    }

    /**
     * @inheritdoc
     */
    protected function convertOutput(Reader $xml)
    {
        $xmlArray = $xml->parse();
        $files = [];

        foreach (ArrayHelper::ensure($xmlArray['value']) as $fileTag) {
            if ($fileTag['name'] != '{}file') {
                continue;
            }

            $fileName = $fileTag['attributes']['name'];

            if (!isset($files[$fileName])) {
                $files[$fileName] = [];
            }

            foreach (ArrayHelper::ensure($fileTag['value']) as $issueTag) {
                $line = $issueTag['attributes']['beginline'];

                if (!isset($files[$fileName][$line])) {
                    $files[$fileName][$line] = [];
                }

                $files[$fileName][$line] = [
                    'tool' => 'PHPMessDetector',
                    'type' => $issueTag['attributes']['rule'],
                    'message' => $issueTag['value'],
                ];
            }
        }

        return $files;
    }
}
