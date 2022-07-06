<?php

# boilerplates

// replace Exporter
\ryunosuke\PHPUnit\Exporter\Exporter::insteadOf();

// add exclude directory
$excludeList = new \PHPUnit\Util\ExcludeList();
$excludeList->getExcludedDirectories();
$excludeList->addDirectory(__DIR__ . '/../src');

// declare that function
/**
 * @template T
 * @param T $value
 * @return T|\ryunosuke\PHPUnit\Actual
 */
function that($value)
{
    return new \ryunosuke\PHPUnit\Actual($value);
}
