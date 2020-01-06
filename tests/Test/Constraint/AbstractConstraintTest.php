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

    function test_extractCallable()
    {
        $constraint = new ConcreteConstraint();

        [$callable, $args, $string] = $constraint->extractCallable([$callee = 'strlen', new \stdClass()]);
        $this->assertSame('strlen', $callable);
        $this->assertEquals([new \stdClass()], $args);
        $this->assertEquals('strlen(\\stdClass)', $string);

        [$callable, $args, $string] = $constraint->extractCallable([$callee = [$this, 'getName'], 1, 2, 3]);
        $this->assertSame($callee, $callable);
        $this->assertEquals([1, 2, 3], $args);
        $this->assertEquals(__CLASS__ . '::getName(1, 2, 3)', $string);

        [$callable, $args, $string] = $constraint->extractCallable([$callee = function () { }, 4, 5, 6]);
        $this->assertSame($callee, $callable);
        $this->assertEquals([4, 5, 6], $args);
        $this->assertStringContainsString(basename(__FILE__), $string);

        $object = new class()
        {
            function method() { }

            function __invoke() { }
        };
        [$callable, $args, $string] = $constraint->extractCallable([$callee = [$object, 'method'], 7, 8, 9]);
        $this->assertSame($callee, $callable);
        $this->assertEquals([7, 8, 9], $args);
        $this->assertStringContainsString(basename(__FILE__), $string);
        $this->assertStringContainsString('::method', $string);

        [$callable, $args, $string] = $constraint->extractCallable([$callee = $object, 7, 8, 9]);
        $this->assertSame($callee, $callable);
        $this->assertEquals([7, 8, 9], $args);
        $this->assertStringContainsString(basename(__FILE__), $string);
        $this->assertStringContainsString('__invoke', $string);
    }
}

class ConcreteConstraint extends AbstractConstraint
{
    public function failLogically(Constraint $failed, $other, $description, ExpectationFailedException $prev = null)
    {
        return parent::failLogically(...func_get_args());
    }

    public function extractCallable($other): array
    {
        return parent::extractCallable(...func_get_args());
    }
}
