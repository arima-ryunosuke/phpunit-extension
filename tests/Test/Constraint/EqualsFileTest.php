<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\EqualsFile;
use SplFileObject;

class EqualsFileTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename = sys_get_temp_dir() . '/tmp.txt', 'dummy');

        $constraint = new EqualsFile(new SplFileObject($filename));
        $this->assertFalse($constraint->evaluate('', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('dummy', '', true));

        $this->assertEquals("is equal to 'dummy'", $constraint->toString());

        $this->ng(function () use ($constraint) {
            $constraint->evaluate('x');
        }, "'x' is equal to 'dummy'");
    }
}
