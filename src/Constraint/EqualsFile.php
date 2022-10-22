<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;
use SplFileInfo;

class EqualsFile extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        if ($value instanceof SplFileInfo) {
            $value = $value->getPathname();
        }
        parent::__construct(new IsEqual(file_get_contents($value), 0.0, 10, $ignoreCase));
    }
}
