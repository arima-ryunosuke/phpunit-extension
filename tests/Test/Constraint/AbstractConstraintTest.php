<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\AbstractConstraint;

class AbstractConstraintTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_toString()
    {
        $constraint = new ConcreteConstraint();

        $this->assertEquals('concrete constraint', $constraint->toString());
    }
}

class ConcreteConstraint extends AbstractConstraint
{
}
