<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\ExpectationFailedException;

class OutputMatches extends AbstractConstraint
{
    private $expected;

    public function __construct($value)
    {
        $this->expected = $value;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);
        return sprintf('%s %s', $string, $this->toString());
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            ob_start();
            $callable(...$args);
            $regex = new RegularExpression($this->expected);
            return $regex->evaluate(ob_get_contents(), $description, $returnResult);
        }
        catch (ExpectationFailedException $e) {
            return $this->fail($other, $description, $e->getComparisonFailure());
        }
        finally {
            ob_end_clean();
        }
    }

    public function toString(): string
    {
        return 'output is ' . $this->exporter()->export($this->expected);
    }
}
