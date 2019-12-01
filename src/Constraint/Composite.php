<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

abstract class Composite extends Constraint
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
            return $this->innerConstraint()->evaluate($other, $description, $returnResult);
        }
        catch (ExpectationFailedException $e) {
            $this->fail($other, $description, $e->getComparisonFailure());
        }
    }// @codeCoverageIgnore

    public function count(): int
    {
        return \count($this->innerConstraint());
    }

    public function toString(): string
    {
        return $this->innerConstraint()->toString();
    }

    protected function innerConstraint(): Constraint
    {
        return $this->innerConstraint;
    }
}
