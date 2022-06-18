<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\SubsetMatches;

class SubsetMatchesTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $patterns = [
            '#hogera#',
            'a' => '#world#',
            'b' => '#world#i',
        ];
        $constraint = new SubsetMatches($patterns);
        $this->assertTrue($constraint->evaluate([
            'a' => 'hello world',
            'b' => 'end of WORLD',
            '#hogera#',
            'c' => 'dummy',
            'd' => 'dummy',
        ], '', true));
        $this->assertFalse($constraint->evaluate([
            '#hogera#',
            'a' => 'hello worker',
            'b' => 'end of worker',
        ], '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate([]);
        }, 'containes #hogera#');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(['#hogera#']);
        }, 'actual[a] exists');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(['#hogera#', 'a' => []]);
        }, 'actual[a] must be stringable');

        $this->ng(function () use ($constraint) {
            $constraint->evaluate(['#hogera#', 'a' => 'hoge']);
        }, ' actual[a] matches #world#');
    }

    function test_assert()
    {
        that([
            'BEGIN',
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ])->subsetMatches([
            '#^insert into#i',
            '#^delete from#i',
        ]);
    }
}
