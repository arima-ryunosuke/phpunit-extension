<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\LogicalAnd;

class IsBetween extends Composite
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;

        parent::__construct(LogicalAnd::fromConstraints(Assert::greaterThanOrEqual($this->min), Assert::lessThanOrEqual($this->max)));
    }

    protected function failureDescription($other): string
    {
        return sprintf("%s %s [%s ~ %s]", $other, $this->toString(), $this->min, $this->max);
    }

    public function toString(): string
    {
        return 'is between';
    }
}
