<?php

namespace ryunosuke\Test\Constraint;

use ryunosuke\PHPUnit\Constraint\MatchesCountEquals;

class MatchesCountEqualsTest extends \ryunosuke\Test\AbstractTestCase
{
    function test()
    {
        $patternCounts = [
            '#begin#i'  => 1,
            '#insert#i' => 2,
            '#delete#i' => null,
            '#commit#i' => 1,
        ];
        $constraint = new MatchesCountEquals($patternCounts);
        $this->assertTrue($constraint->evaluate([
            'BEGIN',
            'INSERT INTO t_table',
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ], '', true));
        $this->assertTrue($constraint->evaluate([
            'BEGIN',
            'INSERT INTO t_table',
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ], '', true));
        $this->assertFalse($constraint->evaluate([
            'BEGIN',
            'BEGIN',
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ], '', true));
        $this->assertFalse($constraint->evaluate([
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ], '', true));
        $this->assertFalse($constraint->evaluate([
            'DELETE FROM t_table',
            'COMMIT',
        ], '', true));

        $this->ng(function () use ($constraint) {
            $constraint->evaluate([
                'DELETE FROM t_table',
                'COMMIT',
            ]);
        }, 'matches count equals');
    }

    function test_assert()
    {
        that([
            'BEGIN',
            'INSERT INTO t_table',
            'DELETE FROM t_table',
            'DELETE FROM t_table',
            'COMMIT',
        ])->matchesCountEquals([
            '#^insert into#i' => 1,
            '#^delete from#i' => null,
        ]);
    }
}
