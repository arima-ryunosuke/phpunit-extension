<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\OutputMatches;

class OutputMatchesTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new OutputMatches('#hoge#');
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { echo 'xyz'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'hogera'; }, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { });
        }, "output is '#hoge#'");
    }
}
