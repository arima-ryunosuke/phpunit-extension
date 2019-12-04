<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsBlank;

class IsBlankTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsBlank(false);
        $this->assertFalse($constraint->evaluate(' ', '', true));

        $constraint = new IsBlank();
        $this->assertTrue($constraint->evaluate([], '', true));
        $this->assertTrue($constraint->evaluate(null, '', true));
        $this->assertTrue($constraint->evaluate(false, '', true));
        $this->assertTrue($constraint->evaluate('', '', true));
        $this->assertTrue($constraint->evaluate(' ', '', true));
        $this->assertFalse($constraint->evaluate([1], '', true));
        $this->assertFalse($constraint->evaluate(0, '', true));
        $this->assertFalse($constraint->evaluate(0.0, '', true));
        $this->assertFalse($constraint->evaluate(true, '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertFalse($constraint->evaluate(' x ', '', true));
        $this->assertFalse($constraint->evaluate(new \stdClass(), '', true));
        $this->assertFalse($constraint->evaluate(new \Exception(), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(0);
        }, '0 is Blank');
    }
}
