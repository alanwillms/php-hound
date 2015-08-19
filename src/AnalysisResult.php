<?php
namespace phphound;

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

        return $data;
    }
}
