<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;
use SplFileInfo;

class EqualsPath extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual($this->filter($value), 0.0, 10, $ignoreCase));
    }

    protected function filter($other)
    {
        if ($other instanceof SplFileInfo) {
            $other = $other->getPathname();
        }
        return strtr($other, [
            DIRECTORY_SEPARATOR => '/',
        ]);
    }
}
