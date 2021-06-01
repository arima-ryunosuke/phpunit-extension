<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\Contains;

class ContainsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        file_put_contents($filename1 = sys_get_temp_dir() . '/tmp1.txt', '123abc');
        file_put_contents($filename2 = sys_get_temp_dir() . '/tmp2.txt', ' 789xyz');

        $constraint = new Contains('789', true);
        $this->assertFalse($constraint->evaluate($filename1, '', true));
        $this->assertTrue($constraint->evaluate($filename2, '', true));
        $this->assertFalse($constraint->evaluate('A123Z', '', true));
        $this->assertTrue($constraint->evaluate('A789Z', '', true));
        $this->assertFalse($constraint->evaluate(['123', '456', 789], '', true));
        $this->assertTrue($constraint->evaluate(['456', '789'], '', true));

        $constraint = new Contains(' 789', false);
        $this->assertTrue($constraint->evaluate($filename2, '', true));
        $this->assertTrue($constraint->evaluate(' 789', '', true));
        $this->assertTrue($constraint->evaluate([789], '', true));

        $this->ng(function () use ($constraint, $filename1) {
            $constraint->evaluate($filename1);
        }, 'contains " 789"');
    }
}
