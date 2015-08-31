<?php
namespace phphound\output;

use League\Plates\Engine;
use phphound\AnalysisResult;
use phphound\output\html\FileHighlighter;
use phphound\output\html\History;
use DateTime;
use SplFileObject;

class HtmlOutput extends AbstractOutput implements TriggerableInterface
{
    use TextTriggerTrait;

    /**
     * Plates engine used to output HTML.
     * @var Engine Plates engine instance.
     */
    protected $platesEngine;

    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        $this->cli->br();
        $this->cli->inline('Writing HTML report in "./phphound/"... ');
        $history = new History($this->getOutputDirectory());
        $history->append($result);
        foreach ($result->toArray() as $filePath => $lines) {
            $this->writeFileHtml($filePath, $lines);
        }
        $this->writeIndexHtml($result, $history);
        $history->save();
        $this->cli->out('Done!');
    }

    /**
     * Create HTML report index page.
     * @param AnalysisResult $result result data object.
     * @return void
     */
    protected function writeIndexHtml(AnalysisResult $result, History $history)
    {
        $files = [];

        foreach ($result->toArray() as $fileName => $lines) {
            if (!isset($files[$fileName])) {
                $files[$fileName] = 0;
            }

            foreach ($lines as $issues) {
                $files[$fileName] += count($issues);
            }
        }

        $chartData = $history->getData();
        $indexHtml = $this->renderView(
            'index',
            [
                'files' => $files,
                'executions' => $chartData['executions'],
                'historyData' => array_values($chartData['historyData']),
            ]
        );
        $fileName = $this->getOutputDirectory() . '/index.html';
        $file = new SplFileObject($fileName, 'w');
        $file->fwrite($indexHtml);
    }

    /**
     * Create HTML report for one file.
     * @param string $phpFilePath analyzed PHP file path.
     * @param array $lines lines with their issues.
     * @return void
     */
    protected function writeFileHtml($phpFilePath, $lines)
    {
        $highlighter = new FileHighlighter($phpFilePath, $lines);
        $fileHtml = $this->renderView(
            'file',
            [
                'fileName' => $phpFilePath,
                'lines' => $lines,
                'fileContent' => $highlighter->getHtml(),
                'backButton' => true,
            ]
        );
        $htmlFileName = $this->getOutputDirectory() . '/'
            . str_replace(DIRECTORY_SEPARATOR, '_', $phpFilePath) . '.html';

        $file = new SplFileObject($htmlFileName, 'w');
        $file->fwrite($fileHtml);
    }

    /**
     * Render a HTML view within the layout.
     * @param string $view view file name.
     * @param array $data variables required by view file.
     * @return string output HTML.
     */
    protected function renderView($view, $data)
    {
        $date = new DateTime;
        $content = $this->getPlatesEngine()->render($view, $data);

        return $this->getPlatesEngine()->render(
            'layout',
            [
                'content' => $content,
                'phpHoundVersion' => 'PHP Hound 0.4.0',
                'phpVersion' => 'PHP ' . phpversion(),
                'generationTime' => $date->format('r'),
                'backButton' => !empty($data['backButton']),
            ]
        );
    }

    /**
     * Configure and return Plates engine.
     * @return Engine Plates engine instance.
     */
    protected function getPlatesEngine()
    {
        if (null === $this->platesEngine) {
            $this->platesEngine = new Engine(__DIR__ . '/../templates');
        }

        return $this->platesEngine;
    }

    /**
     * Get output directory.
     * @return string output directory path.
     */
    protected function getOutputDirectory()
    {
        $directory = $this->outputDirectory . '/phphound';

        if (!file_exists($directory)) {
            mkdir($directory);
        }

        return $directory;
    }
}
