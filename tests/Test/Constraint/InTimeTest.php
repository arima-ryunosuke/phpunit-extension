<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\InTime;

class InTimeTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new InTime(0.5);
        $this->assertFalse($constraint->evaluate(function () { usleep(600 * 1000); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { usleep(400 * 1000); }, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { usleep(600 * 1000); });
        }, "less than 0.5 second");
    }
}
