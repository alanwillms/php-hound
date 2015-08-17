<?php
namespace phphound\output;

use League\Plates\Engine;
use phphound\AnalysisResult;
use DateTime;
use SplFileObject;

class HtmlOutput extends TextOutput
{
    protected $platesEngine;

    /**
     * @inheritdoc
     */
    public function result(AnalysisResult $result)
    {
        $this->cli->br();
        $this->cli->inline('Writing HTML report in "./phphound/"... ');
        $this->writeIndexHtml($result);
        foreach ($result->toArray() as $filePath => $lines) {
            $this->writeFileHtml($filePath, $lines);
        }
        $this->cli->out('Done!');
    }

    /**
     * Create HTML report index page.
     * @param AnalysisResult $result result data object.
     * @return void
     */
    protected function writeIndexHtml(AnalysisResult $result)
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

        $indexHtml = $this->renderView(
            'index',
            ['files' => $files]
        );
        $fileName = $this->getOutputDirectory() . '/index.html';
        touch($fileName);
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
        $fileHtml = $this->renderView(
            'file',
            [
                'fileName' => $phpFilePath,
                'lines' => $lines,
                'fileContent' => $this->highlightFileWithLineNumbers($phpFilePath, $lines),
                'backButton' => true,
            ]
        );
        $htmlFileName = $this->getOutputDirectory() . '/'
            . str_replace(DIRECTORY_SEPARATOR, '_', $phpFilePath) . '.html';

        touch($htmlFileName);
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

    /**
     * Highlight PHP file showing issues and line numbers.
     * @param string $filePath analyzed file path.
     * @param array $linesWithIssues lines of code containing issues.
     * @return string HTML.
     */
    protected function highlightFileWithLineNumbers($filePath, array $linesWithIssues)
    {
        $highlight = "<code><span style=\"color: #000000\">";
        $code = substr(highlight_file($filePath, true), 36, -15);
        $lines = explode('<br />', $code);
        $lineCount = count($lines);
        $paddingLength = strlen($lineCount);

        foreach ($lines as $i => $line) {
            $lineNumber = $i + 1;
            $paddedLineNumber = str_pad($lineNumber, $paddingLength, '0', STR_PAD_LEFT);
            $hasIssues = isset($linesWithIssues[$lineNumber]);
            $lineCssClass = $hasIssues ? 'has-issues' : 'no-issues';
            $lineId = 'line' . $lineNumber;

            $highlight .= '<div class="' . $lineCssClass . '" id="' . $lineId . '">';
            $highlight .= '<span class="line-number">' . $paddedLineNumber . '</span>';

            if ($hasIssues) {
                $highlight .= $this->getIssuesTooltip($lineNumber, $linesWithIssues[$lineNumber]);
            }

            $highlight .= $line . '</div>';
        }

        $highlight .= "</span></code>";

        return $highlight;
    }

    /**
     * Create MaterialDesign tooltip showing issues for a given line of code.
     * @param integer $lineNumber line number.
     * @param array $issues list of issues for this line.
     * @return string HTML.
     */
    protected function getIssuesTooltip($lineNumber, array $issues)
    {
        $html = '<div class="mdl-tooltip mdl-tooltip--large" for="line' . $lineNumber . '">';
        $html .= '<ul>';

        foreach ($issues as $issue) {
            $html .= '<li>' . trim($issue['message']) . '</ul>';
        }

        $html .= '</ul></div>';

        return $html;
    }
}
