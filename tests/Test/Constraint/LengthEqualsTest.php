<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\LengthEquals;

class LengthEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename1 = sys_get_temp_dir() . '/tmp1.txt', '123');
        file_put_contents($filename2 = sys_get_temp_dir() . '/tmp2.txt', '123456');

        $constraint = new LengthEquals(3);
        $this->assertTrue($constraint->evaluate($filename1, '', true));
        $this->assertFalse($constraint->evaluate($filename2, '', true));
        $this->assertTrue($constraint->evaluate('xyz', '', true));
        $this->assertFalse($constraint->evaluate('AxyzZ', '', true));
        $this->assertTrue($constraint->evaluate([1, 2, 3], '', true));
        $this->assertFalse($constraint->evaluate([1], '', true));
        $this->assertTrue($constraint->evaluate(new \ArrayObject([1, 2, 3]), '', true));
        $this->assertFalse($constraint->evaluate(new \ArrayObject([1]), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(['X' => 'X']);
        }, "actual size 1 matches expected size 3");
    }
}
