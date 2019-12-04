<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsEqualIgnoreWS;

class IsEqualIgnoreWSTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsEqualIgnoreWS('hoge');
        $this->assertFalse($constraint->evaluate('', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('hoge', '', true));
        $this->assertTrue($constraint->evaluate(' hoge ', '', true));
        $this->assertTrue($constraint->evaluate("\n\thoge\t\n", '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' is equal to 'hoge'");
    }
}
