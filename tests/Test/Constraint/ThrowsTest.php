<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\Throws;

class ThrowsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new Throws(
            new \DomainException('message', 123),
            \RuntimeException::class,
            'ex message',
            1
        );
        $this->assertTrue($constraint->evaluate(function () { throw new \DomainException('message', 123); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \RuntimeException(); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \Exception('ex message'); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \Exception('', 1); }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \InvalidArgumentException(); }, '', true));

        $constraint = new Throws(\InvalidArgumentException::class);
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \Exception(); }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \OutOfBoundsException(); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \InvalidArgumentException(); }, '', true));

        $constraint = new Throws('message');
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \Exception('hoge'); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \Exception('message'); }, '', true));

        $constraint = new Throws(123);
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \Exception('', 1); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \Exception('', 123); }, '', true));

        $constraint = new Throws(new \InvalidArgumentException('message', 123));
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \Exception('message', 123); }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \InvalidArgumentException('hoge', 1); }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \InvalidArgumentException('message', 1); }, '', true));
        $this->assertFalse($constraint->evaluate(function () { throw new \InvalidArgumentException('hoge', 123); }, '', true));
        $this->assertTrue($constraint->evaluate(function () { throw new \InvalidArgumentException('message', 123); }, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { });
        }, 'throw \InvalidArgumentException');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { throw new \Exception('message', 123); });
        }, 'throw \InvalidArgumentException');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { throw new \InvalidArgumentException('hoge', 1); });
        }, 'throw \InvalidArgumentException');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { throw new \InvalidArgumentException('message', 1); });
        }, 'throw \InvalidArgumentException');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { throw new \InvalidArgumentException('hoge', 123); });
        }, 'throw \InvalidArgumentException');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate([function () { }, 1, 'str']);
        }, 'Closure::__invoke(1, "str")');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate([function () { }, new \stdClass()]);
        }, 'Closure::__invoke(\\stdClass)');
    }
}
