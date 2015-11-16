<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 10:58 AM
 */

namespace Athletic;

use Athletic\Factories\MethodResultsFactory;
use Athletic\Results\MethodResults;
use JsonSchema\RefResolver;
use ReflectionClass;
use zpt\anno\Annotations;

/**
 * Class AthleticEvent
 * @package Athletic
 */
abstract class AthleticEvent
{
    /** @var MethodResultsFactory */
    private $methodResultsFactory;
    /** @var \Athletic\Benchmarkers\BenchmarkerInterface[]  */
    private $currentBenchmarkers;

    public function __construct()
    {

    }


    protected function classSetUp()
    {

    }


    protected function classTearDown()
    {

    }


    protected function setUp()
    {

    }


    protected function tearDown()
    {

    }

    /**
     * Restart benchmarking
     *
     * @return mixed
     */
    public function restart()
    {
        foreach($this->currentBenchmarkers as $benchmarker) {
            $benchmarker->clean();
        }

        return true;
    }


    /**
     * @param MethodResultsFactory $methodResultsFactory
     */
    public function setMethodFactory($methodResultsFactory)
    {
        $this->methodResultsFactory = $methodResultsFactory;
    }


    /**
     * @return MethodResults[]
     */
    public function run()
    {
        $classReflector   = new ReflectionClass(get_class($this));
        $classAnnotations = new Annotations($classReflector);

        $methodAnnotations = array();
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
        }

        $this->classSetUp();
        $results = $this->runBenchmarks($methodAnnotations);
        $this->classTearDown();

        return $results;
    }


    /**
     * @param Annotations[] $methods
     *
     * @return MethodResults[]
     */
    private function runBenchmarks($methods)
    {
        $results = array();

        foreach ($methods as $methodName => $annotations) {
            if (isset($annotations['iterations']) === true) {
                $results[] = $this->runMethodBenchmark($methodName, $annotations);
            }
        }
        return $results;
    }


    /**
     * @param string $method
     * @param int    $annotations
     *
     * @return MethodResults
     */
    private function runMethodBenchmark($method, $annotations)
    {
        $iterations = $annotations['iterations'];

        if(!isset($annotations['benchmark']) || !$annotations['benchmark']) {
            $annotations['benchmark'] = ['\Athletic\Benchmarkers\TimeBenchmarker'];
        } elseif(!is_array($annotations['benchmark'])) {
            $annotations['benchmark'] = array($annotations['benchmark']);
        }

        $results = $this->benchmarkMethod($method, $iterations, $annotations['benchmark']);

        $finalResults = $this->methodResultsFactory->create($method, $results, $iterations);

        $this->setOptionalAnnotations($finalResults, $annotations);

        return $finalResults;

    }

    /**
     * @param string   $method
     * @param int      $iterations
     * @param string[] $benchmarks      Benchmarker class names
     *
     * @return \Athletic\Benchmarkers\BenchmarkerInterface[]
     */
    private function benchmarkMethod($method, $iterations, $benchmarks)
    {
        /** @var \Athletic\Benchmarkers\BenchmarkerInterface[] $benchs */
        $benchs = array();

        // Create benchmarkers.
        foreach($benchmarks as $benchmark) {
            if($benchmark === 'TimeBenchmarker') {
                $benchmark = '\Athletic\Benchmarkers\TimeBenchmarker';
            } elseif($benchmark === 'MemoryBenchmarker') {
                $benchmark = '\Athletic\Benchmarkers\MemoryBenchmarker';
            }

            $bench = new ReflectionClass($benchmark);
            $benchs[$benchmark] = $bench->newInstance();
        }

        $this->currentBenchmarkers = $benchs;

        // Iterate
        for ($i = 0; $i < $iterations; ++$i) {
            // Trigger event
            $this->setUp();

            // Start benchmark value
            foreach($benchs as $bench) {
                $bench->start();
            }

            // Run the actual method
            $this->$method();

            // Stop benchmark value
            foreach($benchs as $bench) {
                $bench->end();
            }

            // Trigger event
            $this->tearDown();
        }

        $this->currentBenchmarkers = array();

        return $benchs;
    }


    /**
     * @param MethodResults $finalResults
     * @param array         $annotations
     */
    private function setOptionalAnnotations(MethodResults $finalResults, $annotations)
    {
        if (isset($annotations['group']) === true) {
            $finalResults->setGroup($annotations['group']);
        }

        if (isset($annotations['baseline']) === true) {
            $finalResults->setBaseline();
        }

        if (isset($annotations['show'])) {
            if(!is_array($annotations['show'])) {
                $annotations['show'] = array($annotations['show']);
            }

            $finalResults->hideAll();

            foreach($annotations['show'] as $show) {
                $finalResults->showResult($show);
            }
        }

        if (isset($annotations['hide'])) {
            if(!is_array($annotations['hide'])) {
                $annotations['hide'] = array($annotations['hide']);
            }

            foreach($annotations['hide'] as $hide) {
                $finalResults->hideResult($hide);
            }
        }
    }

}