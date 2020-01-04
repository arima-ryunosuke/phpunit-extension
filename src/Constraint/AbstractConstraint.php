<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

abstract class AbstractConstraint extends Constraint
{
    public function toString(): string
    {
        return strtolower(preg_replace('/(?!^)([A-Z])/', ' $0', (new \ReflectionClass($this))->getShortName()));
    }

    protected function failLogically(Constraint $failed, $other, $description, ExpectationFailedException $prev = null)
    {
        $comparisonFailure = $prev ? $prev->getComparisonFailure() : null;
        $failureDescription = sprintf('Failed asserting that %s.', $this->failureDescription($other));
        $theDescription = sprintf(" Failed %s.", $failed->failureDescription($other));
        $additionalDescription = $failed->additionalFailureDescription($other);

        $description = implode("\n", array_filter([
            $description,
            $failureDescription . $theDescription,
            $additionalDescription,
        ], 'strlen'));

        throw new ExpectationFailedException($description, $comparisonFailure, $prev);
    }

    protected function extractArgument($other)
    {
        if (is_callable($other)) {
            $callable = $other;
            $args = [];
        }
        else {
            $callable = $other[0];
            $args = array_slice($other, 1);
        }

        is_callable($callable, null, $name);
        $argstring = implode(', ', array_map(function ($v) {
            if (is_object($v) && !$v instanceof \JsonSerializable) {
                return '\\' . get_class($v);
            }
            return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $args));
        $caller = "$name($argstring)";

        return [$callable, $args, $caller];
    }
}
