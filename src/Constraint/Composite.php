<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

abstract class Composite extends AbstractConstraint
{
    /** @var Constraint */
    private $innerConstraint;

    public function __construct(Constraint $innerConstraint)
    {
        $this->innerConstraint = $innerConstraint;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false)
    {
        try {
            return $this->innerConstraint()->evaluate($this->filter($other), $description, $returnResult);
        }
        catch (ExpectationFailedException $e) {
            return $this->fail($other, $description, $e->getComparisonFailure());
        }
    }

    public function count(): int
    {
        return \count($this->innerConstraint());
    }

    public function toString(): string
    {
        return $this->innerConstraint()->toString();
    }

    protected function additionalFailureDescription($other): string
    {
        return $this->innerConstraint()->additionalFailureDescription($other);
    }

    protected function filter($other)
    {
        return $other;
    }

    protected function innerConstraint(): Constraint
    {
        return $this->innerConstraint;
    }
}
