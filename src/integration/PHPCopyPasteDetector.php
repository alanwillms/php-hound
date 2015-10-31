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
        if (!empty($this->ignoredPaths)) {
            return '--exclude={' . implode(',', $this->ignoredPaths) . '} ';
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getCommand($targetPaths)
    {
        return $this->binariesPath . 'phpcpd '
            . implode(' ', $targetPaths) . ' '
            . $this->getIgnoredArgument() . '--log-pmd="'
            . $this->temporaryFilePath . '"';
    }

    /**
     * @inheritdoc
     */
    protected function addIssuesFromXml(Reader $xml)
    {
        $xmlArray = $xml->parse();

        foreach ((array) $xmlArray['value'] as $duplicationTag) {
            if ($duplicationTag['name'] != '{}duplication'
                || empty($duplicationTag['value'])) {
                continue;
            }

            foreach ((array) $duplicationTag['value'] as $fileTag) {
                if ($fileTag['name'] != '{}file') {
                    continue;
                }

                $fileName = $fileTag['attributes']['path'];
                $line = $fileTag['attributes']['line'];
                $tool = 'PHPCopyPasteDetector';
                $type = 'duplication';
                $message = 'Duplicated code';

                $this->result->addIssue($fileName, $line, $tool, $type, $message);
            }
        }
    }
}
