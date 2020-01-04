<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\StringLengthEquals;

class StringLengthEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new StringLengthEquals(5);
        $this->assertFalse($constraint->evaluate('1234', '', true));
        $this->assertTrue($constraint->evaluate('12345', '', true));
        $this->assertFalse($constraint->evaluate('123456', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' length is 5");

        $constraint = new StringLengthEquals(5, true);
        $this->assertFalse($constraint->evaluate('１２３４', '', true));
        $this->assertTrue($constraint->evaluate('１２３４５', '', true));
        $this->assertFalse($constraint->evaluate('１２３４５６', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' mutibyte length is 5");
    }
}
