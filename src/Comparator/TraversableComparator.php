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
        if ($expected instanceof \Traversable) {
            $expected = iterator_to_array($expected);
        }
        $actual = iterator_to_array($actual);

        if ($canonicalize) {
            if (!\ryunosuke\PHPUnit\is_hasharray($expected)) {
                sort($expected);
            }
            if (!\ryunosuke\PHPUnit\is_hasharray($actual)) {
                sort($actual);
            }
        }

        // no recursive
        $canonicalize = false;

        return parent::assertEquals($expected, $actual, $delta, $canonicalize, $ignoreCase, $processed);
    }
}
