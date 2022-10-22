<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\FileSizeIs;
use SplFileInfo;
use SplFileObject;

class FileSizeIsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename1 = sys_get_temp_dir() . '/tmp1.txt', '123');
        file_put_contents($filename2 = sys_get_temp_dir() . '/tmp2.txt', '123456');

        $constraint = new FileSizeIs(3);
        $this->assertTrue($constraint->evaluate($filename1, '', true));
        $this->assertFalse($constraint->evaluate($filename2, '', true));
        $this->assertTrue($constraint->evaluate(new SplFileObject($filename1), '', true));
        $this->assertTrue($constraint->evaluate(new SplFileInfo($filename1), '', true));

        $this->ng(function () use ($constraint, $filename2) {
            $constraint->evaluate($filename2);
        }, 'size is 3 bytes');
    }
}
