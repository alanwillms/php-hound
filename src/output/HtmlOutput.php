<?php
namespace phphound\output;

use League\Plates\Engine;
use phphound\AnalysisResult;
use DateTime;
use SplFileObject;

class HtmlOutput extends TextOutput
{
    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        $this->cli->br();
        $this->cli->inline('Writing HTML report in "./phphound/"... ');
        $this->writeHtmlReport($result);
        $this->cli->out('Done!');
    }

    protected function writeHtmlReport(AnalysisResult $result)
    {
        $date = new DateTime;
        $templates = new Engine(__DIR__ . '/../templates');
        $html = $templates->render(
            'index',
            [
                'phpHoundVersion' => 'PHP Hound 0.4.0',
                'phpVersion' => 'PHP ' . phpversion(),
                'generationTime' => $date->format('r'),
                'result' => $result->toArray(),
            ]
        );
        $directory = $this->outputDirectory . '/phphound';
        $fileName = $directory . '/index.html';

        if (!file_exists($directory)) {
            mkdir($directory);
        }
        touch($fileName);
        $file = new SplFileObject($fileName, 'w');
        $file->fwrite($html);
    }
}
