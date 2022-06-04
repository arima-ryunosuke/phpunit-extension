<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\IsThrowable;

class IsThrowableTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new IsThrowable();
        $this->assertTrue($constraint->evaluate(new \DomainException(), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('message'), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('', 123), '', true));
        $this->assertTrue($constraint->evaluate(new \Error(), '', true));
        $this->assertFalse($constraint->evaluate(123, '', true));

        $constraint = new IsThrowable('message');
        $this->assertFalse($constraint->evaluate(new \DomainException(), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('message'), '', true));
        $this->assertFalse($constraint->evaluate(new \DomainException('', 123), '', true));
        $this->assertFalse($constraint->evaluate(new \Error(), '', true));

        $constraint = new IsThrowable(123);
        $this->assertFalse($constraint->evaluate(new \DomainException(), '', true));
        $this->assertFalse($constraint->evaluate(new \DomainException('message'), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('', 123), '', true));
        $this->assertFalse($constraint->evaluate(new \Error(), '', true));

        $constraint = new IsThrowable(\DomainException::class);
        $this->assertTrue($constraint->evaluate(new \DomainException(), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('message'), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('', 123), '', true));
        $this->assertFalse($constraint->evaluate(new \Error(), '', true));
        $this->assertFalse($constraint->evaluate(new \RuntimeException(), '', true));

        $constraint = new IsThrowable(new \DomainException('message', 123));
        $this->assertFalse($constraint->evaluate(new \DomainException(), '', true));
        $this->assertFalse($constraint->evaluate(new \DomainException('message'), '', true));
        $this->assertFalse($constraint->evaluate(new \DomainException('', 123), '', true));
        $this->assertTrue($constraint->evaluate(new \DomainException('message', 123), '', true));
        $this->assertFalse($constraint->evaluate(new \Error(), '', true));
        $this->assertFalse($constraint->evaluate(new \RuntimeException(), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(new \RuntimeException());
        }, "to be \DomainException('message', 123)");
    }
}
