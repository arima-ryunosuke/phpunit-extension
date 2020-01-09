<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\HasKey;

class HasKeyTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new HasKey('x');
        $this->assertTrue($constraint->evaluate(['x' => 'X'], '', true));
        $this->assertFalse($constraint->evaluate(['X' => 'X'], '', true));
        $this->assertTrue($constraint->evaluate((object) ['x' => 'X'], '', true));
        $this->assertFalse($constraint->evaluate((object) ['X' => 'X'], '', true));
        $this->assertTrue($constraint->evaluate(new \ArrayObject(['x' => 'X']), '', true));
        $this->assertFalse($constraint->evaluate(new \ArrayObject(['X' => 'X']), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(['X' => 'X']);
        }, "has the key 'x'");
    }
}
