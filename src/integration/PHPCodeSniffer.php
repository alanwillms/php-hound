<?php
namespace phphound\integration;

use Sabre\Xml\Reader;
use phphound\helper\ArrayHelper;

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
        return $this->binariesPath . 'phpcs -p --standard=PSR2 --report=xml '
            . '--report-file="' . $this->temporaryFilePath . '" '. $targetPath;
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
                $line = $issueTag['attributes']['line'];

                if (!isset($files[$fileName][$line])) {
                    $files[$fileName][$line] = [];
                }

                $files[$fileName][$line] = [
                    'tool' => 'PHPCodeSniffer',
                    'type' => $issueTag['attributes']['source'],
                    'message' => $issueTag['value'],
                ];
            }
        }

        return $files;
    }
}
