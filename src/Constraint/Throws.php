<?php

namespace ryunosuke\PHPUnit\Constraint;

use ryunosuke\PHPUnit\Util;

class Throws extends IsThrowable
{
    /** @var \Throwable */
    private $actual;

    protected function failureDescription($other): string
    {
        $string = Util::callableToString($other);

        $base = sprintf('%s %s', $string, $this->toString());
        if ($this->actual instanceof \Throwable) {
            return $base . ' thrown ' . $this->throwableToString($this->actual);
        }
        return $base . ' not thrown';
    }

    protected function matches($other): bool
    {
        try {
            $this->actual = null;
            $other();
            return false;
        }
        catch (\Throwable $actual) {
            $this->actual = $actual;
            return parent::matches($this->actual);
        }
    }

    public function toString(): string
    {
        return 'throw ' . preg_replace('#to be #', '', parent::toString());
    }
}
