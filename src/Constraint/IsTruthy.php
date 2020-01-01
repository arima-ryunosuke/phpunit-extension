<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsTruthy extends AbstractConstraint
{
    protected function matches($other): bool
    {
        return boolval($other) === true;
    }
}
