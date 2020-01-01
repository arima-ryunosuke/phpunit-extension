<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\StringContains;

class FileContains extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new StringContains($value, $ignoreCase));
    }

    protected function filter($other)
    {
        return file_get_contents($other);
    }
}
