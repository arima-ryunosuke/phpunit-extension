<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\StringContains;
use SplFileInfo;

class FileContains extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new StringContains($value, $ignoreCase));
    }

    protected function filter($other)
    {
        if ($other instanceof SplFileInfo) {
            $other = $other->getPathname();
        }
        return file_get_contents($other);
    }
}
