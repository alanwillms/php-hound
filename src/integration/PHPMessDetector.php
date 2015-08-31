<?php
namespace phphound\integration;

use phphound\AnalysisResult;
use phphound\helper\ArrayHelper;
use Sabre\Xml\Reader;

/**
 * Integration of PHPHound with PHPMessDetector.
 * @see https://github.com/phpmd/phpmd
 */
class PHPMessDetector extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'PHPMessDetector';
    }

    /**
     * @inheritdoc
     */
    public function getIgnoredArgument()
    {
        if (!empty($this->ignoredPaths)) {
            return '--exclude ' . implode(',', $this->ignoredPaths) . ' ';
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getCommand($targetPath)
    {
        return $this->binariesPath . 'phpmd ' . $targetPath . ' '
            . 'xml cleancode,codesize,controversial,design,naming,unusedcode '
            . $this->getIgnoredArgument() . '> "'
            . $this->temporaryFilePath . '"';
    }

    /**
     * @inheritdoc
     */
    protected function convertOutput(Reader $xml, AnalysisResult $resultSet)
    {
        $xmlArray = $xml->parse();

        foreach ((array) $xmlArray['value'] as $fileTag) {
            if ($fileTag['name'] != '{}file') {
                continue;
            }

            $fileName = $fileTag['attributes']['name'];

            foreach ((array) $fileTag['value'] as $issueTag) {
                $line = $issueTag['attributes']['beginline'];
                $tool = 'PHPMessDetector';
                $type = $issueTag['attributes']['rule'];
                $message = $issueTag['value'];

                $resultSet->addIssue($fileName, $line, $tool, $type, $message);
            }
        }
    }
}
