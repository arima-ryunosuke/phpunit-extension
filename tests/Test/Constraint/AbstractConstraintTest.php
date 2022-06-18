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

    /** @noinspection PhpUnusedPrivateFieldInspection */
    function test_toArray()
    {
        $constraint = new ConcreteConstraint();

        $array = ['a' => 'A', 'b' => 'B'];
        $this->assertSame($array, $constraint->toArray($array));
        $this->assertSame($array, $constraint->toArray(new \ArrayObject($array)));
        $this->assertSame($array, $constraint->toArray(new \ArrayIterator($array)));
        $this->assertSame($array, $constraint->toArray((object) $array));
        $this->assertSame($array, $constraint->toArray(new class {
            private   $a = 'A';
            protected $b = 'B';
        }));

        $this->assertSame(['X'], $constraint->toArray('X'));
    }
}

class ConcreteConstraint extends AbstractConstraint
{
    public function failLogically(Constraint $failed, $other, $description, ExpectationFailedException $prev = null)
    {
        return parent::failLogically(...func_get_args());
    }

    public function toArray($other): array
    {
        return parent::toArray(...func_get_args());
    }
}
