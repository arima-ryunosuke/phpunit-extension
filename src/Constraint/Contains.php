<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use ryunosuke\PHPUnit\Util;

class Contains extends Composite
{
    private $needle, $strict;

    public function __construct($needle, bool $strict = null)
    {
        parent::__construct();

        $this->needle = $needle;
        $this->strict = $strict;
    }

    protected function detectConstraint($other): Constraint
    {
        $contraints = [];

        if (Util::isStringy($other) && is_readable($other)) {
            $contraints[] = new FileContains($this->needle, $this->strict === null ? false : !$this->strict);
        }

        if (Util::isStringy($other)) {
            $contraints[] = new StringContains($this->needle, $this->strict === null ? false : !$this->strict);
        }

        if (is_iterable($other)) {
            $contraints[] = $this->strict
                ? new TraversableContainsIdentical($this->needle)
                : new TraversableContainsEqual($this->needle);
        }

        return LogicalOr::fromConstraints(...$contraints);
    }
}
