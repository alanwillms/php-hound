<?php
namespace phphound;

use phphound\output\filter\OutputFilterInterface;

/**
 * Code analysis result.
 */
class AnalysisResult
{
    /**
     * Result data.
     * @var array analysis result data.
     */
    protected $data = [];

    /**
     * Analysis result filter.
     * @var OutputFilterInterface filter instance.
     */
    protected $filter;

    /**
     * Register an issue.
     * @param string $fileName File name.
     * @param integer $line  Line number.
     * @param string $toolName Analysis tool name.
     * @param string $issueType Issue type.
     * @param string $message Issue description.
     * @return void
     */
    public function addIssue($fileName, $line, $toolName, $issueType, $message)
    {
        if (!isset($this->data[$fileName])) {
            $this->data[$fileName] = [];
        }

        if (!isset($this->data[$fileName][$line])) {
            $this->data[$fileName][$line] = [];
        }

        $this->data[$fileName][$line][] = [
            'tool' => $toolName,
            'type' => $issueType,
            'message' => $message,
        ];
    }

    /**
     * Check if there are any code issues.
     * @return boolean true if there are issues.
     */
    public function hasIssues()
    {
        return !empty($this->toArray());
    }

    /**
     * Return result data as an array.
     * <code>
     * file_name => [
     *     line_number => [
     *         issue 1,
     *         issue 2,
     *         ...
     *     ]
     * ]
     * </code>
     * @return array result daya.
     */
    public function toArray()
    {
        $data = [];

        foreach ($this->data as $fileName => $lines) {
            ksort($lines);
            $data[$fileName] = $lines;
        }

        ksort($data);

        if (null !== $this->filter) {
            $data = $this->filter->filter($data);
        }

        return $data;
    }

    /**
     * Merge the data of another result into this one.
     * @param AnalysisResult $other another analysis result object.
     * @return AnalysisResult returns itself.
     */
    public function mergeWith(AnalysisResult $other)
    {
        foreach ($other->toArray() as $fileName => $lines) {
            foreach ($lines as $line => $issues) {
                foreach ($issues as $issue) {
                    $this->addIssue(
                        $fileName,
                        $line,
                        $issue['tool'],
                        $issue['type'],
                        $issue['message']
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Add an output filter to delegate to the analysis result object.
     * @param OutputFilterInterface $filter filter instance.
     */
    public function setResultsFilter(OutputFilterInterface $filter)
    {
        $this->filter = $filter;
    }
}
