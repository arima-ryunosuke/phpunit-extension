<?php

namespace ryunosuke\PHPUnit\Constraint;

class InTime extends AbstractConstraint
{
    private $time;

    public function __construct(float $time)
    {
        $this->time = $time;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);
        return sprintf('%s %s', $string, $this->toString());
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        $mtime = microtime(true);
        $callable(...$args);
        return microtime(true) - $mtime < $this->time;
    }

    public function toString(): string
    {
        return 'processing time is less than ' . $this->time . ' seconds';
    }
}
