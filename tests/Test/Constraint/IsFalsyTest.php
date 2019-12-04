<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsFalsy;

class IsFalsyTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsFalsy();
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertFalse($constraint->evaluate(1, '', true));
        $this->assertFalse($constraint->evaluate(new \stdClass(), '', true));
        $this->assertTrue($constraint->evaluate([], '', true));
        $this->assertTrue($constraint->evaluate(null, '', true));
        $this->assertTrue($constraint->evaluate(0, '', true));
        $this->assertTrue($constraint->evaluate("0", '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' is Falsy");
    }
}
