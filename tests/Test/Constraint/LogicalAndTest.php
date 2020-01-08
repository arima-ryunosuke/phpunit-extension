<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;

class LogicalAndTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_export()
    {
        $this->assertEquals('IsAll', LogicalAnd::export('Is'));
        $this->assertEquals('isAll', LogicalAnd::export('is'));
        $this->assertEquals('IsEqualAll', LogicalAnd::export('IsEqual'));
        $this->assertEquals('isEqualAll', LogicalAnd::export('isEqual'));
        $this->assertEquals('PrefixIsAll', LogicalAnd::export('PrefixIs'));
        $this->assertEquals('prefixIsAll', LogicalAnd::export('prefixIs'));
        $this->assertEquals('ArrayHasKeyAll', LogicalAnd::export('ArrayHasKey'));
        $this->assertEquals('arrayHasKeyAll', LogicalAnd::export('arrayHasKey'));
        $this->assertEquals('FileEqualsAll', LogicalAnd::export('FileEquals'));
        $this->assertEquals('fileEqualsAll', LogicalAnd::export('fileEquals'));
        $this->assertEquals('EqualsFileAll', LogicalAnd::export('EqualsFile'));
        $this->assertEquals('equalsFileAll', LogicalAnd::export('equalsFile'));
    }

    function test_import()
    {
        $this->assertEquals('Is', LogicalAnd::import('IsAll'));
        $this->assertEquals('is', LogicalAnd::import('isAll'));
        $this->assertEquals('IsEqual', LogicalAnd::import('IsEqualAll'));
        $this->assertEquals('isEqual', LogicalAnd::import('isEqualAll'));
        $this->assertEquals('PrefixIs', LogicalAnd::import('PrefixIsAll'));
        $this->assertEquals('prefixIs', LogicalAnd::import('prefixIsAll'));
        $this->assertEquals('ArrayHasKey', LogicalAnd::import('ArrayHasKeyAll'));
        $this->assertEquals('arrayHasKey', LogicalAnd::import('arrayHasKeyAll'));
        $this->assertEquals('FileEquals', LogicalAnd::import('FileEqualsAll'));
        $this->assertEquals('fileEquals', LogicalAnd::import('fileEqualsAll'));
        $this->assertEquals('EqualsFile', LogicalAnd::import('EqualsFileAll'));
        $this->assertEquals('equalsFile', LogicalAnd::import('equalsFileAll'));
    }

    function test()
    {
        $constraint = LogicalAnd::fromConstraints(new StringContains('x'), new StringContains('y'), new StringContains('z'));
        $this->assertTrue($constraint->evaluate('xyz', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertFalse($constraint->evaluate('y', '', true));
        $this->assertFalse($constraint->evaluate('z', '', true));

        $this->assertCount(3, $constraint);
        $this->assertEquals('contains "x" and contains "y" and contains "z"', $constraint->toString());

        $constraint->evaluate('xyz');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('');
        }, 'contains "x" and contains "y" and contains "z"');
    }

    function test_misc()
    {
        $this->assertSame($c = new StringContains(''), LogicalAnd::fromConstraints($c));
    }
}
