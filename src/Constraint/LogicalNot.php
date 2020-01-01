<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint as PHPUnitConstraint;
use PHPUnit\Framework\Constraint\Constraint;

class LogicalNot extends AbstractConstraint
{
    /** @var Constraint */
    private $constraint;

    public function __construct($constraint)
    {
        $this->constraint = $constraint;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false)
    {
        $success = !$this->constraint->evaluate($other, $description, true);

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            return $this->fail($other, $description);
        }
    }

    public function count(): int
    {
        return count($this->constraint);
    }

    public function toString(): string
    {
        switch (get_class($this->constraint)) {
            case PHPUnitConstraint\LogicalAnd::class:
            case PHPUnitConstraint\LogicalNot::class:
            case PHPUnitConstraint\LogicalOr::class:
            case LogicalAnd::class:
            case LogicalNot::class:
            case LogicalOr::class:
                return 'not( ' . $this->constraint->toString() . ' )';

            default:
                return PHPUnitConstraint\LogicalNot::negate($this->constraint->toString());
        }
    }

    protected function failureDescription($other): string
    {
        switch (get_class($this->constraint)) {
            case PHPUnitConstraint\LogicalAnd::class:
            case PHPUnitConstraint\LogicalNot::class:
            case PHPUnitConstraint\LogicalOr::class:
            case LogicalAnd::class:
            case LogicalNot::class:
            case LogicalOr::class:
                return 'not( ' . $this->constraint->failureDescription($other) . ' )';

            default:
                return PHPUnitConstraint\LogicalNot::negate($this->constraint->failureDescription($other));
        }
    }
}
