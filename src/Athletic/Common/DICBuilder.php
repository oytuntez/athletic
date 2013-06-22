<?php
/**
 * User: zach
 * Date: 6/22/13
 * Time: 5:33 PM
 */

namespace Athletic\Common;


use Athletic\Athletic;

class DICBuilder
{
    /** @var  Athletic */
    private $athletic;

    public function __construct($athletic)
    {
        $this->athletic = $athletic;
    }

    public function buildDependencyGraph()
    {

        $this->setupDiscovery();
        $this->setupParser();
        $this->setupCmdLine();
        $this->setupFormatter();
        $this->setupParser();
        $this->setupPublisher();
        $this->setupSuiteRunner();
        $this->setupClassRunner();
        $this->setupMethodResults();
        $this->setupClassResults();

    }

    private function setupClassRunner()
    {
        $this->athletic['classRunnerClass'] = '\Athletic\Runners\ClassRunner';
        $this->athletic['classRunner']      = function ($dic) {
            return function ($class) use ($dic) {
                return new $dic['classRunnerClass']($dic['methodResultsFactory'], $class);
            };
        };

        $this->athletic['classRunnerFactoryClass'] = '\Athletic\Factories\ClassRunnerFactory';
        $this->athletic['classRunnerFactory']      = function ($dic) {
            return new $dic['classRunnerFactoryClass']($dic);
        };
    }


    private function setupDiscovery()
    {
        $this->athletic['discoveryClass'] = '\Athletic\Discovery\RecursiveFileLoader';
        $this->athletic['discovery']      = function ($dic) {
            /** @var CmdLine $cmdLine */
            $cmdLine = $dic['cmdLine'];
            $path = $cmdLine->getSuitePath();
            return new $dic['discoveryClass']($dic['parserFactory'], $path);
        };


    }


    private function setupParser()
    {
        $this->athletic['parserFactoryClass'] = '\Athletic\Factories\ParserFactory';
        $this->athletic['parserFactory']      = function ($dic) {
            return new $dic['parserFactoryClass']($dic);
        };

        $this->athletic['parserClass'] = '\Athletic\Discovery\Parser';
        $this->athletic['parser']      = function ($dic) {
            return function ($path) use ($dic) {
                return new $dic['parserClass']($path);
            };
        };


    }

    private function setupClassResults()
    {
        $this->athletic['classResultsFactoryClass'] = '\Athletic\Factories\ClassResultsFactory';
        $this->athletic['classResultsFactory']      = function ($dic) {
            return new $dic['classResultsFactoryClass']($dic);
        };

        $this->athletic['classResultsClass'] = '\Athletic\Results\ClassResults';
        $this->athletic['classResults']      = function ($dic) {
            return function ($name, $results) use ($dic) {
                return new $dic['classResultsClass']($name, $results);
            };
        };
    }

    private function setupMethodResults()
    {
        $this->athletic['methodResultsFactoryClass'] = '\Athletic\Factories\MethodResultsFactory';
        $this->athletic['methodResultsFactory']      = function ($dic) {
            return new $dic['methodResultsFactoryClass']($dic);
        };

        $this->athletic['methodResultsClass'] = '\Athletic\Results\MethodResults';
        $this->athletic['methodResults']      = function ($dic) {
            return function ($name, $results, $iterations) use ($dic) {
                return new $dic['methodResultsClass']($name, $results, $iterations);
            };
        };
    }
    
    private function setupCmdLine()
    {

        $this->athletic['cmdLine'] = function ($dic) {
            $cmdLine =  new CmdLine();
            $cmdLine->parseArgs();
            return $cmdLine;
        };
    }

    private function setupFormatter()
    {


        $this->athletic['formatterClass'] = '\Athletic\Formatters\DefaultFormatter';
        $this->athletic['formatter']      = function ($dic) {
            return new $dic['formatterClass']();
        };
    }


    private function setupPublisher()
    {
        $this->athletic['publisherClass'] = '\Athletic\Publishers\StdOutPublisher';
        $this->athletic['publisher']      = function ($dic) {
            return new $dic['publisherClass']($dic['formatter']);
        };
    }



    private function setupSuiteRunner()
    {

        $this->athletic['suiteRunnerClass'] = '\Athletic\Runners\SuiteRunner';
        $this->athletic['suiteRunner']      = function ($dic) {
            return new $dic['suiteRunnerClass']($dic['publisher'], $dic['classResultsFactory'], $dic['classRunnerFactory']);
        };

    }
}