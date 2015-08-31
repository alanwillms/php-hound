<?php
namespace phphound\output\html;

use phphound\AnalysisResult;
use SplFileObject;

/**
 * Stores information about multiple builds.
 */
class History
{
    /**
     * Cached history data (not persisted yet).
     * @var array history data.
     */
    protected $cachedData;

    /**
     * Set dependencies.
     * @param string $outputDirectory target directory path.
     */
    public function __construct($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * Append analysis result data to the history.
     * @param AnalysisResult $result analysis result.
     * @return void
     */
    public function append(AnalysisResult $result)
    {
        $data = $this->getData();

        $data['executions'][] = date('M d H:i');

        $toolsIssues = [];

        foreach ($result->toArray() as $lines) {
            foreach ($lines as $issues) {
                foreach ($issues as $issue) {
                    if (!isset($toolsIssues[$issue['tool']])) {
                        $toolsIssues[$issue['tool']] = 0;
                    }
                    $toolsIssues[$issue['tool']]++;
                }
            }
        }

        foreach ($toolsIssues as $tool => $issues) {
            if (!isset($data['historyData'][$tool])) {
                $data['historyData'][$tool] = [
                    'name' => $tool,
                    'data' => [],
                ];
            }
            $data['historyData'][$tool]['data'][] = $issues;
        }

        $this->setData($data);
    }

    /**
     * Stores history data.
     * @return boolean true if successfully wrote to the JSON file.
     */
    public function save()
    {
        $file = new SplFileObject($this->getHistoryFilePath(), 'w');
        return $file->fwrite(json_encode($this->getData()));
    }

    /**
     * Loads history data from the JSON file (or the cache if already did).
     * @return array
     */
    public function getData()
    {
        if (null === $this->cachedData) {
            $data = $this->getHistoryFileContent();
            if (null === $data) {
                $data = [
                    'executed_at' => time(),
                    'data' => [],
                ];
            }
            $this->cachedData = $data;
        }
        return $this->cachedData;
    }

    /**
     * Overwrite current data.
     * @param array $data history data.
     */
    protected function setData(array $data)
    {
        $this->cachedData = $data;
    }

    /**
     * History data
     * @return array
     */
    protected function getHistoryFileContent()
    {
        if (!file_exists($this->getHistoryFilePath())) {
            return null;
        }
        $content = file_get_contents($this->getHistoryFilePath());
        return json_decode($content, true);
    }

    /**
     * JSON data file path.
     * @return string file path.
     */
    protected function getHistoryFilePath()
    {
        return $this->outputDirectory . '/history.json';
    }
}
