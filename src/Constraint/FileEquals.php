<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;
use SplFileInfo;

class FileEquals extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual($value, 0.0, 10, $ignoreCase));
    }

    protected function filter($other)
    {
        if ($other instanceof SplFileInfo) {
            $other = $other->getPathname();
        }
        return file_get_contents($other);
    }
}
