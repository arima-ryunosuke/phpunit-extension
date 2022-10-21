<?php

namespace ryunosuke\Test\Constraint;

use DomainException;
use Exception;
use LogicException;
use RuntimeException;
use ryunosuke\PHPUnit\Constraint\Is;
use SplFileInfo;
use SplFileObject;

class IsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new Is(3.141592);
        $this->assertFalse($constraint->evaluate(3.141591999, '', true));
        $this->assertTrue($constraint->evaluate(3.141592000, '', true));
        $this->assertTrue($constraint->evaluate(3.141592999, '', true));
        $this->assertFalse($constraint->evaluate(3.141593001, '', true));

        $constraint = new Is('2014/12/24');
        $this->assertFalse($constraint->evaluate('2014/12/23 23:59:59.999', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 00:00:00', '', true));
        $this->assertTrue($constraint->evaluate('2014/12/24 23:59:59.999', '', true));
        $this->assertFalse($constraint->evaluate('2014/12/25 00:00:00', '', true));

        $constraint = new Is(new SplFileInfo(__FILE__));
        $this->assertTrue($constraint->evaluate(__FILE__, '', true));

        $constraint = new Is(new SplFileObject(__FILE__));
        $this->assertTrue($constraint->evaluate(__FILE__, '', true));

        $constraint = new Is(new LogicException());
        $this->assertFalse($constraint->evaluate(new Exception(), '', true));
        $this->assertFalse($constraint->evaluate(new RuntimeException(), '', true));
        $this->assertTrue($constraint->evaluate(new LogicException(), '', true));
        $this->assertTrue($constraint->evaluate(new DomainException(), '', true));
    }

    function test_assert()
    {
        that('hoge')->isEqual('hoge');
        that(['hoge'])->isEqual(['hoge']);

        that(__FILE__)->is(new SplFileInfo(__FILE__));
        that(__FILE__)->is(new SplFileObject(__FILE__));
    }
}
