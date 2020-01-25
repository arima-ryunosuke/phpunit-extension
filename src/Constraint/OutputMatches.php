<?php

namespace ryunosuke\PHPUnit\Constraint;

class OutputMatches extends AbstractConstraint
{
    private $expected;

    public function __construct($value)
    {
        $this->expected = $value;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);
        return sprintf('%s %s', $string, $this->toString());
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            ob_start();
            $callable(...$args);
            return preg_match($this->expected, ob_get_contents()) > 0;
        }
        finally {
            ob_end_clean();
        }
    }

    public function toString(): string
    {
        return 'output is ' . $this->exporter()->export($this->expected);
    }
}
