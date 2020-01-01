<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsEqualFile;

class IsEqualFileTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename = sys_get_temp_dir() . '/tmp.txt', 'dummy');

        $constraint = new IsEqualFile($filename);
        $this->assertFalse($constraint->evaluate('', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('dummy', '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' is equal to 'dummy'");
    }
}
