<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsBetween;

class IsBetweenTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsBetween(1, 3);
        $this->assertFalse($constraint->evaluate(0, '', true));
        $this->assertTrue($constraint->evaluate(1, '', true));
        $this->assertTrue($constraint->evaluate(2, '', true));
        $this->assertTrue($constraint->evaluate(3, '', true));
        $this->assertFalse($constraint->evaluate(4, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(0);
        }, '0 is between [1 ~ 3]');
    }
}
