<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;

class SubsetEquals extends Composite
{
    private $subset;
    private $canonicalize;

    public function __construct($subset, bool $canonicalize = false)
    {
        parent::__construct();

        $this->subset = $subset;
        $this->canonicalize = $canonicalize;
    }

    protected function detectConstraint($other): Constraint
    {
        $value = array_replace_recursive($this->toArray($other), $this->toArray($this->subset));
        return new IsEqual($value, 0.0, $this->canonicalize);
    }

    protected function failureDescription($other): string
    {
        return 'an array ' . $this->toString();
    }

    public function toString(): string
    {
        return 'has the subset';
    }
}
