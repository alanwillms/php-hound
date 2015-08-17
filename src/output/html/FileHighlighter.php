<?php
namespace phphound\output\html;

/**
 * Format PHP source code highlighting its quality issues.
 */
class FileHighlighter
{
    /**
     * Analyzed file.
     * @var string file path;
     */
    protected $filePath;

    /**
     * Lines of code with quality issues.
     * @var array lines with respective issues.
     */
    protected $linesWithIssues;

    /**
     * Set dependencies.
     * @param string $filePath analyzed file path.
     * @param array $linesWithIssues lines with respective issues.
     */
    public function __construct($filePath, array $linesWithIssues)
    {
        $this->filePath = $filePath;
        $this->linesWithIssues = $linesWithIssues;
    }

    /**
     * Highlight PHP file showing issues and line numbers.
     * @return string HTML.
     */
    public function getHtml()
    {
        $html = "<code><span style=\"color: #000000\">";
        $paddingLength = $this->getLineNumberPaddingLength();

        foreach ($this->getFormattedPHPFileLines() as $i => $line) {
            $lineNumber = $i + 1;
            $paddedLineNumber = str_pad($lineNumber, $paddingLength, '0', STR_PAD_LEFT);
            $hasIssues = isset($this->linesWithIssues[$lineNumber]);
            $lineCssClass = $hasIssues ? 'has-issues' : 'no-issues';
            $lineId = 'line' . $lineNumber;

            $html .= '<div class="' . $lineCssClass . '" id="' . $lineId . '">';
            $html .= '<span class="line-number">' . $paddedLineNumber . '</span>';
            $html .= $this->getIssuesTooltip($lineNumber);
            $html .= $line . '</div>';
        }

        $html .= "</span></code>";

        return $html;
    }

    /**
     * Ammount of characters of the number of the last line of code.
     * @return integer padding length.
     */
    protected function getLineNumberPaddingLength()
    {
        $lines = $this->getFormattedPHPFileLines();
        $lineCount = count($lines);
        return strlen($lineCount);
    }

    /**
     * Split all formatted PHP lines of code into an array.
     * @return string[] HTML splitted into an array.
     */
    protected function getFormattedPHPFileLines()
    {
        $code = substr(highlight_file($this->filePath, true), 36, -15);
        return explode('<br />', $code);
    }

    /**
     * Create MaterialDesign tooltip showing issues for a given line of code.
     * @param integer $lineNumber line number.
     * @return string HTML.
     */
    protected function getIssuesTooltip($lineNumber)
    {
        if (!isset($this->linesWithIssues[$lineNumber])) {
            return '';
        }
        $html = '<div class="mdl-tooltip mdl-tooltip--large" for="line' . $lineNumber . '">';
        $html .= '<ul>';

        foreach ($this->linesWithIssues[$lineNumber] as $issue) {
            $html .= '<li>' . trim($issue['message']) . '</ul>';
        }

        $html .= '</ul></div>';

        return $html;
    }
}