<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\JsonMatchesArray;

class JsonMatchesArrayTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $constraint = new JsonMatchesArray(['a' => 'A', 'b' => 'B']);
        $this->assertTrue($constraint->evaluate(json_encode(['a' => 'A', 'b' => 'B']), '', true));
        $this->assertTrue($constraint->evaluate(json_encode(['b' => 'B', 'a' => 'A']), '', true));
        $this->assertFalse($constraint->evaluate(json_encode(['a' => 'A', 'b' => 'B', 'c' => 'C']), '', true));
        $this->assertFalse($constraint->evaluate(json_encode(['a' => 'A']), '', true));
        $this->assertFalse($constraint->evaluate(json_encode(['a' => 'X', 'b' => 'Y']), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(json_encode(['X']));
        }, 'matches JSON string "{"a":"A","b":"B"}"');

        $constraint = new JsonMatchesArray(['a' => 'A', 'b' => 'B', 'X' => ['y' => 'Y']], true);
        $this->assertTrue($constraint->evaluate(json_encode(['a' => 'A', 'b' => 'B', 'X' => ['y' => 'Y']]), '', true));
        $this->assertTrue($constraint->evaluate(json_encode(['b' => 'B', 'a' => 'A', 'X' => ['y' => 'Y']]), '', true));
        $this->assertTrue($constraint->evaluate(json_encode(['a' => 'A', 'b' => 'B', 'c' => 'C', 'X' => ['y' => 'Y', 'z' => 'Z']]), '', true));
        $this->assertFalse($constraint->evaluate(json_encode(['a' => 'A']), '', true));
        $this->assertFalse($constraint->evaluate(json_encode(['a' => 'X', 'b' => 'Y']), '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(json_encode(['X']));
        }, 'matches JSON string "{"0":"X","a":"A","b":"B","X":{"y":"Y"}}"');
    }

    function test_assert()
    {
        that('{"a": 1}')->jsonMatchesArray(['a' => 1]);
        that('{"a": 1, "b":2}')->jsonMatchesArray(['a' => 1], true);
    }
}
