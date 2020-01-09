<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;

class HasKey extends Composite
{
    private $key;

    public function __construct($key)
    {
        parent::__construct();

        $this->key = $key;
    }

    protected function detectConstraint($other): Constraint
    {
        $contraints = [];

        if (\is_array($other) || $other instanceof \ArrayAccess) {
            $contraints[] = new ArrayHasKey($this->key);
        }
        elseif (is_object($other)) {
            $contraints[] = new ObjectHasAttribute($this->key);
        }

        return LogicalOr::fromConstraints(...$contraints);
    }
}
