<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\FileEquals;

class FileEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename1 = sys_get_temp_dir() . '/tmp1.txt', 'x');
        file_put_contents($filename2 = sys_get_temp_dir() . '/tmp2.txt', 'y');

        $constraint = new FileEquals('x');
        $this->assertTrue($constraint->evaluate($filename1, '', true));
        $this->assertFalse($constraint->evaluate($filename2, '', true));

        $this->ng(function () use ($constraint, $filename2) {
            $constraint->evaluate($filename2);
        }, "is equal to 'x'");
    }
}
