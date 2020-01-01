<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsFalsy extends AbstractConstraint
{
    protected function matches($other): bool
    {
        return boolval($other) === false;
    }
}
