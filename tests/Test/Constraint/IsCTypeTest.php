<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsCType;

class IsCTypeTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsCType(IsCType::CTYPE_DIGIT);
        $this->assertFalse($constraint->evaluate('', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('0', '', true));
        $this->assertTrue($constraint->evaluate('1', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, 'is of ctype "digit"');

        $this->ng(function () {
            new IsCType('hoge');
        }, '<hoge> is not a valid type');
    }
}
