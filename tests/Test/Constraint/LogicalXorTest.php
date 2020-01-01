<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalXor;

class LogicalXorTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = LogicalXor::fromConstraints(new StringContains('x'), new StringContains('y'), new StringContains('z'));
        $this->assertFalse($constraint->evaluate('abc', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertFalse($constraint->evaluate('xy', '', true));
        $this->assertFalse($constraint->evaluate('xyz', '', true));
        $this->assertTrue($constraint->evaluate('xNz', '', true));
        $this->assertTrue($constraint->evaluate('AyZ', '', true));

        $this->assertCount(3, $constraint);
        $this->assertEquals('contains "x" xor contains "y" xor contains "z"', $constraint->toString());

        $constraint->evaluate('xNz');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('xyz');
        }, 'contains "x" xor contains "y" xor contains "z"');
    }

    function test_misc()
    {
        $this->assertSame($c = new StringContains(''), LogicalXor::fromConstraints($c));
    }
}
