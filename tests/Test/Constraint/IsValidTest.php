<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsValid;

class IsValidTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsValid(IsValid::VALID_IPV4);
        $this->assertFalse($constraint->evaluate(null, '', true));
        $this->assertFalse($constraint->evaluate(123, '', true));
        $this->assertFalse($constraint->evaluate('1.1.1', '', true));
        $this->assertFalse($constraint->evaluate('256.256.256.256', '', true));
        $this->assertFalse($constraint->evaluate('1.1.1.1.', '', true));
        $this->assertTrue($constraint->evaluate('1.1.1.1', '', true));
        $this->assertTrue($constraint->evaluate('255.255.255.255', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(123);
        }, '123 is valid "ipv4"');

        $this->ng(function () {
            new IsValid('hoge');
        }, '<hoge> is not a valid type');
    }
}
