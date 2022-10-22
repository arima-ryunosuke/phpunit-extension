<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;
use SplFileInfo;

class FileSizeIs extends Composite
{
    private $size;

    public function __construct(int $size)
    {
        parent::__construct(new IsEqual($size));

        $this->size = $size;
    }

    protected function filter($other)
    {
        if ($other instanceof SplFileInfo) {
            $other = $other->getPathname();
        }
        return filesize($other);
    }

    protected function failureDescription($other): string
    {
        return sprintf("%s %s %s bytes", $other, $this->toString(), number_format($this->size));
    }

    public function toString(): string
    {
        return "size is";
    }
}
