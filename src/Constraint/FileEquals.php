<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;

class FileEquals extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual($value, 0.0, 10, false, $ignoreCase));
    }

    protected function filter($other)
    {
        return file_get_contents($other);
    }
}
