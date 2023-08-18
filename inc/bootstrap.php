<?php

# boilerplates

// replace Exporter
\ryunosuke\PHPUnit\Exporter\Exporter::insteadOf();

// add exclude directory
$excludeList = new \PHPUnit\Util\ExcludeList();
$excludeList->getExcludedDirectories();
$excludeList->addDirectory(__DIR__ . '/../src');

// register comparator
\SebastianBergmann\Comparator\Factory::getInstance()->register(new \ryunosuke\PHPUnit\Comparator\TraversableComparator());

// declare that function
if (!function_exists('that')) {
    /**
     * @template T
     * @param T $value
     * @return T|\ryunosuke\PHPUnit\Actual|\stub\All
     */
    function that($value)
    {
        return new \ryunosuke\PHPUnit\Actual($value);
    }
}
