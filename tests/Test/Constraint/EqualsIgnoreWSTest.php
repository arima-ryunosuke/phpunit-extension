<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\EqualsIgnoreWS;

class EqualsIgnoreWSTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new EqualsIgnoreWS('hoge');
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
