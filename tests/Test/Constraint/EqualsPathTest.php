<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\EqualsPath;

class EqualsPathTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new EqualsPath(__FILE__);
        $this->assertFalse($constraint->evaluate('/hoge/fuga', '', true));
        $this->assertTrue($constraint->evaluate(__FILE__, true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' is equal to");
    }
}
