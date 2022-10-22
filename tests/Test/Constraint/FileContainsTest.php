<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\FileContains;
use SplFileInfo;
use SplFileObject;

class FileContainsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename1 = sys_get_temp_dir() . '/tmp1.txt', 'abc');
        file_put_contents($filename2 = sys_get_temp_dir() . '/tmp2.txt', 'xyz');

        $constraint = new FileContains('x');
        $this->assertFalse($constraint->evaluate($filename1, '', true));
        $this->assertTrue($constraint->evaluate($filename2, '', true));
        $this->assertTrue($constraint->evaluate(new SplFileObject($filename2), '', true));
        $this->assertTrue($constraint->evaluate(new SplFileInfo($filename2), '', true));

        $this->ng(function () use ($constraint, $filename1) {
            $constraint->evaluate($filename1);
        }, 'contains "x"');
    }
}
