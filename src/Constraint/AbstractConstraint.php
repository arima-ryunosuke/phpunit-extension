<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

abstract class AbstractConstraint extends Constraint
{
    public function toString(): string
    {
        return strtolower(preg_replace('/(?!^)([A-Z])/', ' $0', (new \ReflectionClass($this))->getShortName()));
    }
}
