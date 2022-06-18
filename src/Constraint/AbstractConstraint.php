<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use function ryunosuke\PHPUnit\get_object_properties;

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

    protected function throwableToString(\Throwable $throwable)
    {
        return sprintf('\\%s(%s, %s)',
            get_class($throwable),
            $this->exporter()->export($throwable->getMessage()),
            $this->exporter()->export($throwable->getCode())
        );
    }

    protected function toArray($other): array
    {
        if (is_array($other)) {
            return $other;
        }

        if ($other instanceof \ArrayObject) {
            return $other->getArrayCopy();
        }

        if ($other instanceof \Traversable) {
            return iterator_to_array($other);
        }

        if (is_object($other)) {
            return get_object_properties($other);
        }

        return (array) $other;
    }
}
