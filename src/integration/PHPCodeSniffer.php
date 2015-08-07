<?php
namespace phphound\integration;

use phphound\AnalysisResult;
use phphound\helper\ArrayHelper;
use Sabre\Xml\Reader;

/**
 * Integration of PHPHound with PHPCodeSniffer.
 * @see https://github.com/squizlabs/PHP_CodeSniffer
 */
class PHPCodeSniffer extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'PHPCodeSniffer';
    }

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
    protected function convertOutput(Reader $xml, AnalysisResult $resultSet)
    {
        $xmlArray = $xml->parse();

        foreach (ArrayHelper::ensure($xmlArray['value']) as $fileTag) {
            if ($fileTag['name'] != '{}file') {
                continue;
            }

            $fileName = $fileTag['attributes']['name'];

            foreach (ArrayHelper::ensure($fileTag['value']) as $issueTag) {
                $line = $issueTag['attributes']['line'];
                $tool = 'PHPCodeSniffer';
                $type = $issueTag['attributes']['source'];
                $message = $issueTag['value'];

                $resultSet->addIssue($fileName, $line, $tool, $type, $message);
            }
        }
    }
}
