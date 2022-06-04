<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\ExpectationFailedException;
use ryunosuke\PHPUnit\Constraint\AbstractConstraint;

class AbstractConstraintTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_toString()
    {
        $constraint = new ConcreteConstraint();

        $this->assertEquals('concrete constraint', $constraint->toString());
    }

    function test_failLogically()
    {
        $isEqual = new IsEqual('xyz');
        $other = 'abc';
        try {
            $prev = null;
            $isEqual->evaluate($other, 'description', false);
        }
        catch (ExpectationFailedException $t) {
            $prev = $t;
        }

        try {
            $constraint = new ConcreteConstraint();
            $this->assertEquals('concrete constraint', $constraint->failLogically($isEqual, $other, 'message', $prev));
        }
        catch (ExpectationFailedException $actual) {
            $this->assertStringContainsString("message", $actual->getMessage());
            $this->assertStringContainsString("Failed 'abc' is equal to 'xyz'", $actual->getMessage());
            $this->assertStringNotContainsString('description', $actual->getMessage());
        }
    }
}

class ConcreteConstraint extends AbstractConstraint
{
    public function failLogically(Constraint $failed, $other, $description, ExpectationFailedException $prev = null)
    {
        return parent::failLogically(...func_get_args());
    }
}
