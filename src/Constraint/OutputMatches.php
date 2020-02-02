<?php

namespace ryunosuke\PHPUnit\Constraint;

class OutputMatches extends AbstractConstraint
{
    private $expected;
    private $actual;

    public function __construct($value)
    {
        $this->expected = $value;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);
        return sprintf('%s %s (actual %s)', $string, $this->toString(), $this->exporter()->export($this->actual));
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            $this->actual = null;
            ob_start();
            $callable(...$args);
            $this->actual = ob_get_contents();
            return preg_match($this->expected, $this->actual) > 0;
        }
        finally {
            ob_end_clean();
        }
    }

    public function toString(): string
    {
        return 'output matches ' . $this->exporter()->export($this->expected);
    }
}
