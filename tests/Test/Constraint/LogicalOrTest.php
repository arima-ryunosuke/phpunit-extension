<?php

namespace ryunosuke\Test\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use ryunosuke\PHPUnit\Constraint\LogicalOr;

class LogicalOrTest extends \ryunosuke\Test\AbstractTestCase
{
    function test_export()
    {
        $this->assertEquals('IsAny', LogicalOr::export('Is'));
        $this->assertEquals('isAny', LogicalOr::export('is'));
        $this->assertEquals('IsEqualAny', LogicalOr::export('IsEqual'));
        $this->assertEquals('isEqualAny', LogicalOr::export('isEqual'));
        $this->assertEquals('PrefixIsAny', LogicalOr::export('PrefixIs'));
        $this->assertEquals('prefixIsAny', LogicalOr::export('prefixIs'));
        $this->assertEquals('ArrayHasKeyAny', LogicalOr::export('ArrayHasKey'));
        $this->assertEquals('arrayHasKeyAny', LogicalOr::export('arrayHasKey'));
        $this->assertEquals('FileEqualsAny', LogicalOr::export('FileEquals'));
        $this->assertEquals('fileEqualsAny', LogicalOr::export('fileEquals'));
        $this->assertEquals('EqualsFileAny', LogicalOr::export('EqualsFile'));
        $this->assertEquals('equalsFileAny', LogicalOr::export('equalsFile'));
    }

    function test_import()
    {
        $this->assertEquals('Is', LogicalOr::import('IsAny'));
        $this->assertEquals('is', LogicalOr::import('isAny'));
        $this->assertEquals('IsEqual', LogicalOr::import('IsEqualAny'));
        $this->assertEquals('isEqual', LogicalOr::import('isEqualAny'));
        $this->assertEquals('PrefixIs', LogicalOr::import('PrefixIsAny'));
        $this->assertEquals('prefixIs', LogicalOr::import('prefixIsAny'));
        $this->assertEquals('ArrayHasKey', LogicalOr::import('ArrayHasKeyAny'));
        $this->assertEquals('arrayHasKey', LogicalOr::import('arrayHasKeyAny'));
        $this->assertEquals('FileEquals', LogicalOr::import('FileEqualsAny'));
        $this->assertEquals('fileEquals', LogicalOr::import('fileEqualsAny'));
        $this->assertEquals('EqualsFile', LogicalOr::import('EqualsFileAny'));
        $this->assertEquals('equalsFile', LogicalOr::import('equalsFileAny'));
    }

    function test()
    {
        $constraint = LogicalOr::fromConstraints(new StringContains('x'), new StringContains('y'), new StringContains('z'));
        $this->assertFalse($constraint->evaluate('abc', '', true));
        $this->assertTrue($constraint->evaluate('x', '', true));
        $this->assertTrue($constraint->evaluate('y', '', true));
        $this->assertTrue($constraint->evaluate('z', '', true));

        $this->assertCount(3, $constraint);
        $this->assertEquals('contains "x" or contains "y" or contains "z"', $constraint->toString());

        $constraint->evaluate('xyz');
        $this->ng(function () use ($constraint) {
            $constraint->evaluate('');
        }, 'contains "x" or contains "y" or contains "z"');
    }

    function test_misc()
    {
        $this->assertSame($c = new StringContains(''), LogicalOr::fromConstraints($c));
    }
}
