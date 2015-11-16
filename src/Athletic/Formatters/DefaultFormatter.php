<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:47 PM
 */

namespace Athletic\Formatters;

use Athletic\Results;
use Athletic\Results\ClassResults;
use Athletic\Results\MethodResults;

/**
 * Class DefaultFormatter
 * @package Athletic\Formatters
 */
class DefaultFormatter implements FormatterInterface
{
    /**
     * @param ClassResults[] $results
     *
     * @return string
     */
    public function getFormattedResults($results)
    {
        $returnString = "\n";

        $header = array(
            'Method Name',
            'Iterations'
        );

        foreach ($results as $result) {
            $returnString .= $result->getClassName() . "\n";

            // build a table containing the formatted numbers
            $table = array();
            foreach ($result as $methodResult) {
                /** @var $methodResult MethodResults */
                $formattedResult = $methodResult->getResults();
                $header = array_merge($header, array_keys($formattedResult));
                $table[] = array_merge([$methodResult->methodName], [$methodResult->iterations], array_values($formattedResult));
            }

            // determine column widths for table layout
            $lengths = array_map('strlen', $header);
            foreach ($table as $row) {
                foreach ($row as $name => $value) {
                    $lengths[$name] = max(strlen($value), $lengths[$name]);
                }
            }

            // format header and table rows
            $returnString .= vsprintf(
                " ".str_repeat("   %s", count($header))."\n",
                array_map(function($index) use($header, $lengths) {
                    return str_pad($header[$index], $lengths[$index]);
                }, array_keys($header))
            );

            $returnString .= vsprintf(
                "    " . str_repeat(" %s", count($lengths)) . "\n",
                array_map(function ($index) use ($lengths) {
                    if ($index === 0) {
                        return str_repeat('-', $lengths[$index] + 1);
                    } else {
                        return str_repeat('-', $lengths[$index] + 2);
                    }

                }, array_keys($lengths))
            );

            foreach ($table as $row) {
                $returnString .= vsprintf(
                    "    %s:".str_repeat(" [%s]", (count($header)-1))."\n",
                    array_map(function($index) use($row, $lengths) {
                        if($index === 1) {
                            return str_pad($row[$index], $lengths[$index], ' ', STR_PAD_LEFT);
                        } else {
                            return str_pad($row[$index], $lengths[$index]);
                        }
                    }, array_keys($header))
                );
            }

            $returnString .= "\n\n";
        }

        return $returnString;
    }
}