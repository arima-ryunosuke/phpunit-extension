<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use ryunosuke\PHPUnit\Util;

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

    protected function extractCallable($other): array
    {
        // detect invoker
        if (is_callable($other)) {
            $callable = $other;
            $args = [];
        }
        else {
            $callable = $other[0];
            $args = array_slice($other, 1);
        }

        // detect call name
        $callname = Util::callableToString($callable);

        // detect argument string
        $argstring = implode(', ', array_map(function ($v) {
            if (is_object($v) && !$v instanceof \JsonSerializable) {
                return '\\' . get_class($v);
            }
            return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $args));

        return [$callable, $args, "$callname($argstring)"];
    }

    protected function throwableToString(\Throwable $throwable)
    {
        return sprintf('\\%s(%s, %s)',
            get_class($throwable),
            $this->exporter()->export($throwable->getMessage()),
            $this->exporter()->export($throwable->getCode())
        );
    }
}
