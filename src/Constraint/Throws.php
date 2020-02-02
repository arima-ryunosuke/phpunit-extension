<?php

namespace ryunosuke\PHPUnit\Constraint;

class Throws extends AbstractConstraint
{
    /** @var \Throwable */
    private $expected;

    /** @var \Throwable */
    private $actual;

    public function __construct($expected)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() > 1) {
            trigger_error('use anyThrows', E_USER_DEPRECATED);
            $this->expected = func_get_args();
            return;
        }
        // @codeCoverageIgnoreEnd

        // for compatible
        $this->expected = [$expected];
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);

        $base = sprintf('%s %s', $string, $this->toString());
        if ($this->actual instanceof \Throwable) {
            return $base . ' thrown ' . $this->throwableToString($this->actual);
        }
        return $base . ' not thrown';
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            $this->actual = null;

            $callable(...$args);
        }
        catch (\Throwable $actual) {
            $this->actual = $actual;

            // for compatible
            foreach ($this->expected as $expected) {
                $isThrowable = new IsThrowable($expected);
                if ($isThrowable->evaluate($this->actual, '', true)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function toString(): string
    {
        // for compatible
        $expecteds = [];
        foreach ($this->expected as $expected) {
            $isThrowable = new IsThrowable($expected);
            $expecteds[] = preg_replace('#to be #', '', $isThrowable->toString());
        }
        return 'throw ' . implode(' or ', $expecteds);
    }
}
