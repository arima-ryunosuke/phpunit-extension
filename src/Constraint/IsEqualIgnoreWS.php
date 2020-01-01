<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;

class IsEqualIgnoreWS extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual($this->filter($value), 0.0, 10, false, $ignoreCase));
    }

    protected function filter($other)
    {
        return trim(preg_replace('#\\s+#u', " ", $other));
    }
}
