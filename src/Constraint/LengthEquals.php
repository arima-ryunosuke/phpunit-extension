<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\Count;
use function ryunosuke\PHPUnit\is_stringable;

class LengthEquals extends Composite
{
    private $length;

    public function __construct(int $length)
    {
        parent::__construct();

        $this->length = $length;
    }

    protected function detectConstraint($other): Constraint
    {
        $contraints = [];

        if (is_iterable($other) || $other instanceof \Countable) {
            $contraints[] = new Count($this->length);
        }

        if (is_stringable($other)) {
            $contraints[] = new StringLengthEquals($this->length);
        }

        if (is_stringable($other) && is_readable($other)) {
            $contraints[] = new FileSizeIs($this->length);
        }

        return LogicalOr::fromConstraints(...$contraints);
    }
}
