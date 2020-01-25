<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use ryunosuke\PHPUnit\Constraint\Composite;

class CompositeTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new class extends Composite {
            protected function detectConstraint($other): Constraint
            {
                return new IsEqual(0);
            }
        };

        $this->assertCount(1, $constraint);
        $this->assertEquals('is true', $constraint->toString());
        $this->assertTrue($constraint->evaluate(0, '', true));
        $this->assertFalse($constraint->evaluate(1, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(1);
        }, "1 is equal to 0");
    }
}
