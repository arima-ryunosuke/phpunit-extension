<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\ClosesTo;

class ClosesToTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new ClosesTo(1.234, 0.0001);
        $this->assertFalse($constraint->evaluate(1.233999, '', true));
        $this->assertTrue($constraint->evaluate(1.234000, '', true));
        $this->assertTrue($constraint->evaluate(1.234099, '', true));
        $this->assertFalse($constraint->evaluate(1.234100, '', true));

        $constraint = new ClosesTo(3.141592);
        $this->assertFalse($constraint->evaluate(3.141591999, '', true));
        $this->assertTrue($constraint->evaluate(3.141592000, '', true));
        $this->assertTrue($constraint->evaluate(3.141592999, '', true));
        $this->assertFalse($constraint->evaluate(3.141593001, '', true));

        $constraint = new ClosesTo(-3.141592);
        $this->assertFalse($constraint->evaluate(-3.141591999, '', true));
        $this->assertTrue($constraint->evaluate(-3.141592000, '', true));
        $this->assertTrue($constraint->evaluate(-3.141592999, '', true));
        $this->assertFalse($constraint->evaluate(-3.141593001, '', true));

        $constraint = new ClosesTo("-3.141592");
        $this->assertFalse($constraint->evaluate(-3.141591999, '', true));
        $this->assertTrue($constraint->evaluate(-3.141592000, '', true));
        $this->assertTrue($constraint->evaluate(-3.141592999, '', true));
        $this->assertFalse($constraint->evaluate(-3.141593001, '', true));

        $constraint = new ClosesTo(0.000_000_005);
        $this->assertFalse($constraint->evaluate(0.000_000_004999, '', true));
        $this->assertTrue($constraint->evaluate(0.000_000_005000, '', true));
        $this->assertTrue($constraint->evaluate(0.000_000_005999, '', true));
        $this->assertFalse($constraint->evaluate(0.000_000_00600, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(0);
        }, "with delta <0.000000001>");
    }

    function test_assert()
    {
        that(3.141592653)->closesTo(3);
        that(3.141592653)->closesTo(3.1);
        that(3.141592653)->closesTo(3.14);
        that(3.141592653)->closesTo(3.141);
        that(3.141592653)->closesTo(3.1415);
        that(3.141592653)->closesTo(3.14159);
        that(3.141592653)->closesTo(3.141592);
        that(3.141592653)->closesTo(3.1415926);
        that(3.141592653)->closesTo(3.14159265);
        that(3.141592653)->closesTo(3.141592653);
    }
}
