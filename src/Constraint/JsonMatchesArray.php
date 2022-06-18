<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\JsonMatches;

class JsonMatchesArray extends Composite
{
    private $expected;
    private $subset;

    public function __construct(array $expected, bool $subset = false)
    {
        parent::__construct();

        $this->expected = $expected;
        $this->subset = $subset;
    }

    protected function detectConstraint($other): Constraint
    {
        if ($this->subset) {
            $value = array_replace_recursive(json_decode($other, true), $this->expected);
            return new JsonMatches(json_encode($value));
        }
        else {
            return new JsonMatches(json_encode($this->expected));
        }
    }
}
