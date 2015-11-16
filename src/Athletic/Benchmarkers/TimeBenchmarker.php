<?php
/**
 * User: oytun
 * Date: 11/16/15
 * Time: 10:54 AM
 */

namespace Athletic\Benchmarkers;

/**
 * Benchmarks the time consumed by the method being benchmarked.
 *
 * Class TimeBenchmarker
 * @package Athletic\Benchmarkers
 */
class TimeBenchmarker implements BenchmarkerInterface
{
    /**
     * @var float
     */
    private $startTime;
    /**
     * @var float
     */
    private $endTime;
    /**
     * @var float
     */
    private $avgCalibration = 0;
    /**
     * @var array
     */
    private $results = array();

    /**
     * TimeBenchmarker constructor.
     * @param int $iterations
     */
    public function __construct($iterations = 1)
    {
        $this->avgCalibration = $this->getCalibrationTime($iterations);
    }

    /**
     * Start benchmarking the time consumed by the method
     */
    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * End benchmarking the time consumed by the method.
     *
     * @return float
     */
    public function end()
    {
        $this->endTime = microtime(true);

        $result = $this->getLastResult();
        $this->results[] = $result;

        return $result;
    }

    /**
     * Clean benchmark values
     *
     * @param bool $cleanCalibration
     * @return bool
     */
    public function clean($cleanCalibration = false)
    {
        $this->startTime = 0;
        $this->endTime = 0;
        $this->results = array();

        if($cleanCalibration) {
            $this->avgCalibration = 0;
        }

        return true;
    }

    /**
     * Returns the last timing
     *
     * @return float
     */
    public function getLastResult()
    {
        return ($this->endTime - $this->startTime) - $this->avgCalibration;
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
        $sum = array_sum($this->getResults());
        $count = count($this->getResults());

        return array(
            'Total Time' => $sum,
            'Average Time' => ($sum / $count),
            'Ops/second' => ($sum == 0.0) ? NAN : ($count / $sum),
        );
    }

    /**
     * @param int $iterations
     *
     * @return float
     */
    private function getCalibrationTime($iterations)
    {
        $resultsCalibration     = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $this->start();
            $this->emptyCalibrationMethod();
            $resultsCalibration[$i] = $this->end();
        }
        $calibration = array_sum($resultsCalibration) / count($resultsCalibration);

        $this->clean();

        return $calibration;
    }


    /**
     * empty method. used to calibrate timing.
     */
    private function emptyCalibrationMethod()
    {

    }

}