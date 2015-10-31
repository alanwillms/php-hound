<?php
namespace phphound\output\filter;

/**
 * Output filters limit the data returned by a \phphound\AnalysisResult object.
 */
interface OutputFilterInterface
{
    /**
     * Filter data returned by a \phphound\AnalysisResult object.
     * @param $data array a list of the file paths and their issues.
     * @return array filtered data array.
     */
    public function filter($data);
}
