<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 2:02 PM
 */

namespace Athletic\Results;

/**
 * Class Results
 * @package Athletic
 */
class MethodResults
{
    public $methodName;
    public $results;
    public $iterations;
    public $group;
    public $baseline = false;

    /**
     * Formatted (key => value) results provided by the benchmarkers of this result set
     *
     * @var array
     */
    public $formattedResults = array();
    public $formattedResultsBackup = array();

    public function __construct($name, $results, $iterations)
    {
        /** @var \Athletic\Benchmarkers\BenchmarkerInterface[] $results */
        $this->methodName = $name;
        $this->results    = $results;
        $this->iterations = $iterations;

        foreach($results as $result) {
            $this->formattedResults = array_merge($this->formattedResults, $result->getFormattedResults());
        }

        $this->baseline   = false;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function setBaseline()
    {
        $this->baseline = true;
    }

    /**
     * Get formatted (key => value) results provided by the benchmarkers of this result set
     *
     * @return array
     */
    public function getResults()
    {
        return $this->formattedResults;
    }

    public function hideAll()
    {
        $this->formattedResultsBackup = $this->formattedResults;
        $this->formattedResults = array();

        return true;
    }

    public function hideResult($name)
    {
        if(is_array($this->formattedResults) && isset($this->formattedResults[$name])) {
            // Let's backup our result, just in case...
            if(!$this->formattedResultsBackup) {
                $this->formattedResultsBackup = $this->formattedResults;
            }

            unset($this->formattedResults[$name]);

            return true;
        }

        return false;
    }

    public function showResult($name)
    {
        if(is_array($this->formattedResultsBackup) && isset($this->formattedResultsBackup[$name])) {
            $this->formattedResults[$name] = $this->formattedResultsBackup[$name];

            return true;
        }

        return false;
    }
}