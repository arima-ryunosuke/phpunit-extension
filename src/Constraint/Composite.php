<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\ExpectationFailedException;

abstract class Composite extends AbstractConstraint
{
    /** @var Constraint */
    private $staticConstraint, $dynamicConstraint;

    public function __construct(Constraint $innerConstraint = null)
    {
        $this->staticConstraint = $innerConstraint;
        $this->dynamicConstraint = null;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false)
    {
        $this->dynamicConstraint = null;

        try {
            return $this->innerConstraint($other)->evaluate($this->filter($other), $description, $returnResult);
        }
        catch (ExpectationFailedException $e) {
            return $this->fail($other, $description, $e->getComparisonFailure());
        }
    }

    public function count(): int
    {
        return count($this->innerConstraint());
    }

    public function toString(): string
    {
        return $this->innerConstraint()->toString();
    }

    protected function failureDescription($other): string
    {
        return $this->innerConstraint($other)->failureDescription($other);
    }

    protected function additionalFailureDescription($other): string
    {
        return $this->innerConstraint($other)->additionalFailureDescription($other);
    }

    protected function innerConstraint($other = null): Constraint
    {
        if ($this->staticConstraint !== null) {
            return $this->staticConstraint;
        }

        if (func_num_args() === 0) {
            return new IsTrue(); // for count method
        }

        return $this->dynamicConstraint = $this->detectConstraint($other);
    }

    protected function detectConstraint($other): Constraint { }

    protected function filter($other)
    {
        return $other;
    }
}
