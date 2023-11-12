<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsTypeOf;
use stdClass;

class IsTypeOfTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsTypeOf('?array|string|stdClass');
        $this->assertTrue($constraint->evaluate([], '', true));
        $this->assertTrue($constraint->evaluate(null, '', true));
        $this->assertTrue($constraint->evaluate('hoge', '', true));
        $this->assertTrue($constraint->evaluate(new stdClass(), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(123);
        }, '123 is of type "null" or is of type "array" or is of type "string" or is instance of class "stdClass"');
    }
}
