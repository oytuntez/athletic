<?php
/**
 * User: oytun
 * Date: 11/16/15
 * Time: 10:51 AM
 */

namespace Athletic\Benchmarkers;

/**
 * Benchmarkers provide benchmarking methods
 *
 * Interface BenchmarkerInterface
 * @package Athletic\Benchmarkers
 */
interface BenchmarkerInterface
{
    /**
     * Start the benchmark
     *
     * @return boolean
     */
    public function start();

    /**
     * End the benchmark
     *
     * @return $this->getResult()
     */
    public function end();

    /**
     * Clean benchmark values, useful to restart benchmarking.
     *
     * @return mixed
     */
    public function clean();

    /**
     * Return the last result of this benchmark, which is usually the value difference between start() and end().
     * @return float
     */
    public function getLastResult();

    /**
     * Return a list of getLastResult()s. All benchmarks measured in this instance.
     *
     * @return mixed
     */
    public function getResults();

    /**
     * Returns an array of header titles and values.
     *
     * @return mixed
     */
    public function getFormattedResults();

}