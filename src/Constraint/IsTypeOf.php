<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsType;

class IsTypeOf extends Composite
{
    public function __construct(string $type)
    {
        $type = strtr($type, ['?' => 'null|']);
        $types = array_unique(preg_split('#\\|#', $type, -1, PREG_SPLIT_NO_EMPTY));

        $constraints = [];
        foreach ($types as $type) {
            try {
                $constraint = new IsType($type);
            }
            catch (\PHPUnit\Framework\Exception $e) {
                $constraint = new IsInstanceOf($type);
            }
            $constraints[] = $constraint;
        }

        parent::__construct(LogicalOr::fromConstraints(...$constraints));
    }
}
