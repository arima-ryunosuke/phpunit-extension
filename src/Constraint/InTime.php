<?php

namespace ryunosuke\PHPUnit\Constraint;

class InTime extends AbstractConstraint
{
    private $time;
    private $actual;

    public function __construct(float $time)
    {
        $this->time = $time;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);
        if ($this->actual instanceof \Throwable) {
            return sprintf('%s is unable to measure time because %s thrown', $string, $this->throwableToString($this->actual));
        }
        return sprintf('%s %s (elapsed time %s seconds)', $string, $this->toString(), number_format($this->actual, 3));
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            $this->actual = null;
            $mtime = microtime(true);
            $callable(...$args);
            $this->actual = microtime(true) - $mtime;
            return $this->actual < $this->time;
        }
        catch (\Exception $e) {
            $this->actual = $e;
            return false;
        }
    }

    public function toString(): string
    {
        return 'processing time is less than ' . $this->time . ' seconds';
    }
}
