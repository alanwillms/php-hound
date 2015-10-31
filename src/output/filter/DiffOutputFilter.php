<?php
namespace phphound\output\filter;

use SebastianBergmann\Diff\Line;

/**
 * Limit AnalysisResult data by only the files and their lines touched by
 * the diff. For example: if the diff only touches a "costumer.php" file
 * at lines 10 and 11, the AnalysisResult will only return issues found on
 * that file and at those lines of code. All other issues will be ignored.
 */
class DiffOutputFilter implements OutputFilterInterface
{
    /**
     * Root path to which the diff file paths are relative.
     * @var string root path.
     */
    protected $root;

    /**
     * An array of Diff objects.
     * @var SebastianBergmann\Diff\Diff[] array of diff objects.
     */
    protected $diffs;

    /**
     * Constructor.
     * @param string $root root path.
     * @param SebastianBergmann\Diff\Diff[] $diffs array of diff objects.
     */
    public function __construct($root, array $diffs)
    {
        $this->root = $root;
        $this->diffs = $diffs;
    }

    /**
     * @inheritdoc
     */
    public function filter($data)
    {
        $filteredData = [];

        foreach ($this->getTouchedFilesAndLines() as $fileName => $lines) {
            if (!isset($data[$fileName])) {
                continue;
            }
            foreach ($lines as $line) {
                if (!isset($data[$fileName][$line])) {
                    continue;
                }
                if (!isset($filteredData[$fileName])) {
                    $filteredData[$fileName] = [];
                }
                $filteredData[$fileName][$line] = $data[$fileName][$line];
            }
        }
        return $filteredData;
    }

    /**
     * Gets the list of files and lines touched by the diff.
     * @return array where the key is the file path and its values the lines.
     */
    protected function getTouchedFilesAndLines()
    {
        $resultFilter = [];
        foreach ($this->diffs as $fileDiff) {
            $file = $this->root . DIRECTORY_SEPARATOR . substr($fileDiff->getTo(), 2);
            $lines = [];
            foreach ($fileDiff->getChunks() as $chunkDiff) {
                $counter = -1;
                foreach ($chunkDiff->getLines() as $lineDiff) {
                    if ($lineDiff->getType() == Line::REMOVED) {
                        continue;
                    }
                    $counter++;

                    // Only display results for touched lines
                    if ($lineDiff->getType() == Line::ADDED) {
                        $lines[] = $chunkDiff->getStart() + $counter;
                    }
                }
            }
            $resultFilter[$file] = $lines;
        }
        return $resultFilter;
    }
}
