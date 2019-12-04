<?php

namespace ryunosuke\Test;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    function ng($callback, $message)
    {
        try {
            $callback();
            $this->fail('not thrown');
        }
        catch (\Throwable $t) {
            $this->assertStringContainsString($message, $t->getMessage());
        }
    }
}
