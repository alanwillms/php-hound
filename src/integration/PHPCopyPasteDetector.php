<?php
namespace phphound\integration;

use phphound\AnalysisResult;
use phphound\helper\ArrayHelper;
use Sabre\Xml\Reader;

/**
 * Integration of PHPHound with PHPCopyPasteDetector.
 * @see https://github.com/sebastianbergmann/phpcpd
 */
class PHPCopyPasteDetector extends AbstractIntegration
{
    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'PHPCopyPasteDetector';
    }

    /**
     * @inheritdoc
     */
    public function getIgnoredArgument()
    {
        if ($this->ignoredPaths) {
            return '--exclude={' . implode(',', $this->ignoredPaths) . '} ';
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getCommand($targetPath)
    {
        return $this->binariesPath . 'phpcpd ' . $targetPath . ' '
            . $this->getIgnoredArgument() . '--log-pmd="'
            . $this->temporaryFilePath . '"';
    }

    /**
     * @inheritdoc
     */
    protected function convertOutput(Reader $xml, AnalysisResult $resultSet)
    {
        $xmlArray = $xml->parse();

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
                $tool = 'PHPCopyPasteDetector';
                $type = 'duplication';
                $message = 'Duplicated code';

                $resultSet->addIssue($fileName, $line, $tool, $type, $message);
            }
        }
    }
}
