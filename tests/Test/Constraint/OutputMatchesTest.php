<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\OutputMatches;

class OutputMatchesTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new OutputMatches('ho*ge', true, ['', '\\z']);
        $this->assertFalse($constraint->evaluate(function () { echo 'ho*geZ'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'Aho*ge'; }, '', true));

        $constraint = new OutputMatches('ho*ge', true, ['\\A', '']);
        $this->assertFalse($constraint->evaluate(function () { echo 'Aho*ge'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'ho*geZ'; }, '', true));

        $constraint = new OutputMatches('ho*ge', true, ['\\A', '\\z']);
        $this->assertFalse($constraint->evaluate(function () { echo 'Aho*geZ'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'ho*ge'; }, '', true));

        $constraint = new OutputMatches('ho*ge', true);
        $this->assertFalse($constraint->evaluate(function () { echo 'xyz'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'ho*gera'; }, '', true));

        $constraint = new OutputMatches('#hoge#');
        $this->assertFalse($constraint->evaluate(function () { }, '', true));
        $this->assertFalse($constraint->evaluate(function () { echo 'xyz'; }, '', true));
        $this->assertTrue($constraint->evaluate(function () { echo 'hogera'; }, '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(function () { });
        }, "output matches '#hoge#'");
    }

    function test_assert()
    {
        that(fn() => print('hogeZ'))->outputStartsWith('hoge');
        that(fn() => print('Ahoge'))->outputEndsWith('hoge');
        that(fn() => print('AhogeZ'))->outputEquals('AhogeZ');
    }
}
