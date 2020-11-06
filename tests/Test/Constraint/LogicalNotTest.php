<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalAnd;
use ryunosuke\PHPUnit\Constraint\LogicalNot;

class LogicalNotTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_export()
    {
        $this->assertEquals('IsNot', LogicalNot::export('Is'));
        $this->assertEquals('isNot', LogicalNot::export('is'));
        $this->assertEquals('IsNotEqual', LogicalNot::export('IsEqual'));
        $this->assertEquals('isNotEqual', LogicalNot::export('isEqual'));
        $this->assertEquals('PrefixIsNot', LogicalNot::export('PrefixIs'));
        $this->assertEquals('prefixIsNot', LogicalNot::export('prefixIs'));
        $this->assertEquals('ArrayNotHasKey', LogicalNot::export('ArrayHasKey'));
        $this->assertEquals('arrayNotHasKey', LogicalNot::export('arrayHasKey'));
        $this->assertEquals('FileNotEquals', LogicalNot::export('FileEquals'));
        $this->assertEquals('fileNotEquals', LogicalNot::export('fileEquals'));
        $this->assertEquals('NotEqualsFile', LogicalNot::export('EqualsFile'));
        $this->assertEquals('notEqualsFile', LogicalNot::export('equalsFile'));
        $this->assertEquals('notInTime', LogicalNot::export('inTime'));
        $this->assertEquals('NotInTime', LogicalNot::export('InTime'));
    }

    function test_import()
    {
        $this->assertEquals('Is', LogicalNot::import('IsNot'));
        $this->assertEquals('is', LogicalNot::import('isNot'));
        $this->assertEquals('IsEqual', LogicalNot::import('IsNotEqual'));
        $this->assertEquals('isEqual', LogicalNot::import('isNotEqual'));
        $this->assertEquals('PrefixIs', LogicalNot::import('PrefixIsNot'));
        $this->assertEquals('prefixIs', LogicalNot::import('prefixIsNot'));
        $this->assertEquals('ArrayHasKey', LogicalNot::import('ArrayNotHasKey'));
        $this->assertEquals('arrayHasKey', LogicalNot::import('arrayNotHasKey'));
        $this->assertEquals('FileEquals', LogicalNot::import('FileNotEquals'));
        $this->assertEquals('fileEquals', LogicalNot::import('fileNotEquals'));
        $this->assertEquals('EqualsFile', LogicalNot::import('NotEqualsFile'));
        $this->assertEquals('equalsFile', LogicalNot::import('notEqualsFile'));
        $this->assertEquals('inTime', LogicalNot::import('notInTime'));
        $this->assertEquals('InTime', LogicalNot::import('NotInTime'));
    }

    function test()
    {
        $constraint = new LogicalNot(new StringContains('x'));
        $this->assertFalse($constraint->evaluate('xyz', '', true));
        $this->assertFalse($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('y', '', true));
        $this->assertTrue($constraint->evaluate('z', '', true));

        $this->assertCount(1, $constraint);
        $this->assertEquals('does not contain "x"', $constraint->toString());

        $constraint->evaluate('abc');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('xyz');
        }, 'does not contain "x"');
    }

    function test_misc()
    {
        $constraint = new LogicalNot(LogicalAnd::fromConstraints(new StringContains('x'), new StringContains('y')));
        $this->assertEquals('not( contains "x" and contains "y" )', $constraint->toString());

        $constraint->evaluate('abc');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('xyz');
        }, 'not( \'xyz\' contains "x" and contains "y" )');
    }
}
