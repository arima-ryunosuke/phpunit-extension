<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsTruthy;

class IsTruthyTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsTruthy();
        $this->assertTrue($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate(1, '', true));
        $this->assertTrue($constraint->evaluate(new \stdClass(), '', true));
        $this->assertFalse($constraint->evaluate([], '', true));
        $this->assertFalse($constraint->evaluate(null, '', true));
        $this->assertFalse($constraint->evaluate(0, '', true));
        $this->assertFalse($constraint->evaluate("0", '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(0);
        }, "0 is Truthy");
    }
}
