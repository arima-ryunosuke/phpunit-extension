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
}
