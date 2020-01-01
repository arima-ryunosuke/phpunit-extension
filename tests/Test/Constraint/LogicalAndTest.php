<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;

class LogicalAndTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = LogicalAnd::fromConstraints(new StringContains('x'), new StringContains('y'), new StringContains('z'));
        $this->assertTrue($constraint->evaluate('xyz', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertFalse($constraint->evaluate('y', '', true));
        $this->assertFalse($constraint->evaluate('z', '', true));

        $this->assertCount(3, $constraint);
        $this->assertEquals('contains "x" and contains "y" and contains "z"', $constraint->toString());

        $constraint->evaluate('xyz');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('');
        }, 'contains "x" and contains "y" and contains "z"');
    }

    function test_misc()
    {
        $this->assertSame($c = new StringContains(''), LogicalAnd::fromConstraints($c));
    }
}
