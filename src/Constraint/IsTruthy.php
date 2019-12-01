<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class IsTruthy extends Constraint
{
    protected function matches($other): bool
    {
        return boolval($other) === true;
    }

    public function toString(): string
    {
        return 'is Truthy';
    }
}
