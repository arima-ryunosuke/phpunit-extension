<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;
use ryunosuke\PHPUnit\Constraint\LogicalNot;

class LogicalNotTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new LogicalNot(new StringContains('x'));
        $this->assertFalse($constraint->evaluate('xyz', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('y', '', true));
        $this->assertTrue($constraint->evaluate('z', '', true));

        $this->assertCount(1, $constraint);
        $this->assertEquals('does not contain "x"', $constraint->toString());

        $constraint->evaluate('abc');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('xyz');
        }, 'does not contain "x"');
    }

    function test_misc()
    {
        $constraint = new LogicalNot(LogicalAnd::fromConstraints(new StringContains('x'), new StringContains('y')));
        $this->assertEquals('not( contains "x" and contains "y" )', $constraint->toString());

        $constraint->evaluate('abc');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('xyz');
        }, 'not( \'xyz\' contains "x" and contains "y" )');
    }
}
