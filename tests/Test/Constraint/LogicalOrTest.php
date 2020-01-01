<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalOr;

class LogicalOrTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = LogicalOr::fromConstraints(new StringContains('x'), new StringContains('y'), new StringContains('z'));
        $this->assertFalse($constraint->evaluate('abc', '', true));
        $this->assertTrue($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('y', '', true));
        $this->assertTrue($constraint->evaluate('z', '', true));

        $this->assertCount(3, $constraint);
        $this->assertEquals('contains "x" or contains "y" or contains "z"', $constraint->toString());

        $constraint->evaluate('xyz');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('');
        }, 'contains "x" or contains "y" or contains "z"');
    }

    function test_misc()
    {
        $this->assertSame($c = new StringContains(''), LogicalOr::fromConstraints($c));
    }
}
