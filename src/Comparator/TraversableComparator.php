<?php
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ryunosuke\PHPUnit\Comparator;

use SebastianBergmann\Comparator\ArrayComparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use function sort;

class TraversableComparator extends ArrayComparator
{
    public function accepts($expected, $actual)
    {
        // $expected can also be iterable
        return is_iterable($expected) && $actual instanceof \Traversable;
    }

    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false, array &$processed = [])
    {
        $expectedKeyValues = $this->entries($expected);
        $actualKeyValues = $this->entries($actual);

        if ($canonicalize) {
            if (!\ryunosuke\PHPUnit\is_hasharray($expectedKeyValues)) {
                sort($expectedKeyValues);
            }
            if (!\ryunosuke\PHPUnit\is_hasharray($actualKeyValues)) {
                sort($actualKeyValues);
            }
        }

        try {
            return parent::assertEquals($expectedKeyValues, $actualKeyValues, $delta, $canonicalize, $ignoreCase, $processed);
        }
        catch (ComparisonFailure $e) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                is_array($expected) ? $e->getExpectedAsString() : preg_replace('#^Array \(\n#', get_class($expected) . " (\n", $e->getExpectedAsString()),
                is_array($actual) ? $e->getActualAsString() : preg_replace('#^Array \(\n#', get_class($actual) . " (\n", $e->getActualAsString()),
                false,
                'Failed asserting that two iterables are equal.'
            );
        }
    }

    protected function entries(iterable $iterable)
    {
        $result = [];
        $arrays = [];
        foreach ($iterable as $k => $v) {
            if (array_key_exists($k, $result)) {
                if (!$arrays[$k]) {
                    $result[$k] = [$result[$k]];
                    $arrays[$k] = true;
                }
                $result[$k][] = $v;
            }
            else {
                $result[$k] = $v;
                $arrays[$k] = false;
            }
        }
        return $result;
    }
}
