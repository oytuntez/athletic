<?php
/**
 * User: oytun
 * Date: 11/16/15
 * Time: 10:54 AM
 */

namespace Athletic\Benchmarkers;

/**
 * Benchmarks the memory consumed by the method being benchmarked.
 *
 * Class MemoryBenchmarker
 * @package Athletic\Benchmarkers
 */
class MemoryBenchmarker implements BenchmarkerInterface
{
    /**
     * @var float
     */
    private $startMemory;
    /**
     * @var float
     */
    private $endMemory;
    /**
     * @var array
     */
    private $results = array();

    /**
     * Start benchmarking the memory consumed by the method
     */
    public function start()
    {
        $this->startMemory = memory_get_usage();
    }

    /**
     * End benchmarking the memory consumed by the method.
     *
     * @return float
     */
    public function end()
    {
        $this->endMemory = memory_get_usage();

        $result = $this->getLastResult();

        $this->results[] = $result;

        return $result;
    }

    /**
     * Clean benchmark values
     *
     * @return bool
     */
    public function clean()
    {
        $this->startMemory = 0;
        $this->endMemory = 0;
        $this->results = array();

        return true;
    }

    /**
     * Returns the last timing
     *
     * @return float
     */
    public function getLastResult()
    {
        return ($this->endMemory - $this->startMemory);
    }

    /**
     * Returns all of the results measured
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Returns an array of header titles and values.
     * @return array
     */
    public function getFormattedResults()
    {
        $first = $this->getResults();

        if(is_array($first) && isset($first[0])) {
            $first = $first[0];
        } else {
            $first = 0;
        }

        $sum = array_sum($this->getResults());
        $count = count($this->getResults());

        return array(
            'First Iteration Memory Usage' => $this->bytesToSize($first),
            'Average Memory' => $this->bytesToSize($sum / $count),
        );
    }

    /**
     * Convert bytes to human readable format
     *
     * @param integer $bytes        Size in bytes to convert
     * @param integer $precision    Decimal precision
     *
     * @return string
     */
    function bytesToSize($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';

        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }
}