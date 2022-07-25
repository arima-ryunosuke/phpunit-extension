<?php

namespace ryunosuke\PHPUnit\Constraint;

use ryunosuke\PHPUnit\Util;

class OutputMatches extends AbstractConstraint
{
    private $expected;
    private $actual;

    public function __construct($value, $raw = false, $with = ['', ''])
    {
        if ($raw) {
            $a = $with[0] ?? '';
            $z = $with[1] ?? '';
            $value = "#$a" . preg_quote($value, '#') . "$z#u";
        }
        $this->expected = $value;
    }

    protected function failureDescription($other): string
    {
        $string = Util::callableToString($other);
        return sprintf('%s %s (actual %s)', $string, $this->toString(), $this->exporter()->export($this->actual));
    }

    protected function matches($other): bool
    {
        try {
            $this->actual = null;
            ob_start();
            $other();
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
