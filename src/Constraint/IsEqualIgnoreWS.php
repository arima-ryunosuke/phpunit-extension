<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsEqual;

class IsEqualIgnoreWS extends Composite
{
    public function __construct($value, bool $ignoreCase = false)
    {
        parent::__construct(new IsEqual($this->stripWhitespace($value), 0.0, 10, false, $ignoreCase));
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        return parent::evaluate($this->stripWhitespace($other), $description, $returnResult);
    }

    private function stripWhitespace($string)
    {
        return trim(preg_replace('#\\s+#u', " ", $string));
    }
}
