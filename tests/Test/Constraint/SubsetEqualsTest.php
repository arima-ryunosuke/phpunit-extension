<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\SubsetEquals;

class SubsetEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $subset = [
            'a' => 'A',
            'b' => 'B',
            'c' => [
                'd' => 1,
                'e' => 2,
            ],
        ];
        $constraint = new SubsetEquals($subset);
        $this->assertFalse($constraint->evaluate([], '', true));
        $this->assertTrue($constraint->evaluate([
            'a' => 'A',
            'b' => 'B',
            'c' => [
                'd' => 1,
                'e' => 2,
                'x' => 'X',
            ],
            'x' => 'X',
        ], '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate([]);
        }, 'an array has the subset');
    }

    function test_assert()
    {
        that([
            'id'        => 123,
            'name'      => 'hoge',
            'update_at' => '2014-12-24 12:34:56',
        ])->subsetEquals([
            'id'   => 123,
            'name' => 'hoge',
        ]);
        that([
            [
                'id'        => 123,
                'name'      => 'hoge',
                'update_at' => '2014-12-24 12:34:56',
            ],
            [
                'id'        => 456,
                'name'      => 'fuga',
                'update_at' => '2015-12-24 12:34:56',
            ],
        ])->subsetEquals([
            [
                'id'   => 123,
                'name' => 'hoge',
            ],
            [
                'id'   => 456,
                'name' => 'fuga',
            ],
        ]);
    }
}
