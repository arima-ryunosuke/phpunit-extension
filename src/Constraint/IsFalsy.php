<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class IsFalsy extends Constraint
{
    protected function matches($other): bool
    {
        return boolval($other) === false;
    }

    public function toString(): string
    {
        return 'is Falsy';
    }
}
