<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;

class IsEqualFile extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual(file_get_contents($value), 0.0, 10, false, $ignoreCase));
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        return parent::evaluate($other, $description, $returnResult);
    }
}
