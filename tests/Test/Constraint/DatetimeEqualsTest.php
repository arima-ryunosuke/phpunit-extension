<?php

namespace ryunosuke\Test\Constraint;

use DateTime;
use ryunosuke\PHPUnit\Constraint\DatetimeEquals;

class DatetimeEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new DatetimeEquals('2014/12/24');
        $this->assertFalse($constraint->evaluate('2014/12/23 23:59:59.999', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 00:00:00', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 23:59:59.999', '', true));
        $this->assertFalse($constraint->evaluate('2014/12/25 00:00:00', '', true));

        $constraint = new DatetimeEquals('2014/12/24', -1);
        $this->assertFalse($constraint->evaluate('2014/12/23 23:59:58.999', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/23 23:59:59.0', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 23:59:59.999', '', true));
        $this->assertFalse($constraint->evaluate('2014/12/25 00:00:00', '', true));

        $constraint = new DatetimeEquals('2014/12/24', +1);
        $this->assertFalse($constraint->evaluate('2014/12/23 23:59:59.999', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 00:00:00.0', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/25 00:00:00.0', '', true));
        $this->assertFalse($constraint->evaluate('2014/12/25 00:00:01', '', true));

        $constraint = new DatetimeEquals(new DateTime('2014/12/24 12:34:56.789'));
        $this->assertFalse($constraint->evaluate('2014/12/24 12:34:56.7889', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 12:34:56.7890', '', true));
        $this->assertFalse($constraint->evaluate('2014/12/24 12:34:56.7999', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('2014/12/25');
        }, "as [2014/12/24 12:34:56.789 ~ 2014/12/24 12:34:56.789)");

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('hogera');
        }, "'hogera' is invalid datetime string");

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(new \stdClass());
        }, "stdClass");
    }

    function test_assert()
    {
        that('2014/12/24 12:34:56')->datetimeEquals(new DateTime('2014/12/24 12:34:56'));
        that(new DateTime('2014/12/24 12:34:56'))->datetimeEquals('2014/12/24');
        that(new DateTime('2014/12/24 12:34:56'))->datetimeEquals('2014/12/24 12:34');
        that(new DateTime('2014/12/24 12:34:56'))->datetimeEquals('2014/12/24 12:34 12:34:56');
        that(new DateTime('2014/12/24 12:34:56'))->datetimeEquals(new DateTime('2014/12/24 12:34:56'));
        that('2014/12/24 12:34:56.000')->datetimeEquals('2014/12/24');
        that('2014/12/24 12:34:56.999')->datetimeEquals('2014/12/24');
        that('2014/12/24 12:34:56.000')->datetimeEquals('2014/12/24 12:34:56');
        that('2014/12/24 12:34:56.999')->datetimeEquals('2014/12/24 12:34:56');
    }
}
