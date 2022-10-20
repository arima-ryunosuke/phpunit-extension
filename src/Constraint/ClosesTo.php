<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\LogicalAnd;

class ClosesTo extends Composite
{
    private $value;
    private $delta;

    public function __construct($value, ?float $delta = null)
    {
        if ($delta === null) {
            $string = (string) $value;
            if (is_float($value) && preg_match('@\.\d+E([-]\d+)@', $string, $matches)) {
                $precision = (int) $matches[1];
                $delta = 10 ** $precision;
            }
            else {
                $value = (float) $value;
                $precision = strlen((explode('.', $string, 2) + [1 => ''])[1]);
                $delta = 10 ** -$precision;
            }
        }
        $delta = abs($delta);

        $this->value = $value;
        $this->delta = $delta;

        if ($value >= 0) {
            parent::__construct(LogicalAnd::fromConstraints(Assert::greaterThanOrEqual($value), Assert::lessThan($value + $delta)));
        }
        else {
            parent::__construct(LogicalAnd::fromConstraints(Assert::greaterThan($value - $delta), Assert::lessThanOrEqual($value)));
        }
    }

    protected function failureDescription($other): string
    {
        return sprintf("%s %s", $this->toFixed($other), $this->toString());
    }

    public function toString(): string
    {
        return sprintf("closes to %s with delta <%s>", $this->toFixed($this->value), $this->toFixed($this->delta));
    }

    private function toFixed($value): string
    {
        if (is_float($value)) {
            if (preg_match('@\.\d+E([+-]\d+)@', $value, $matches)) {
                $number = number_format($value, -$matches[1], '.', '');
                if (strpos($number, '.') !== false) {
                    $value = rtrim(rtrim($number, '0'), '.');
                }
            }
        }
        return $value;
    }
}
