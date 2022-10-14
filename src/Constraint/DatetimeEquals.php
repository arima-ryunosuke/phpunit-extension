<?php

namespace ryunosuke\PHPUnit\Constraint;

use DateTimeInterface;
use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\ExpectationFailedException;
use Throwable;
use function ryunosuke\PHPUnit\date_convert;
use function ryunosuke\PHPUnit\date_fromto;
use function ryunosuke\PHPUnit\date_timestamp;

class DatetimeEquals extends Composite
{
    private $since;
    private $until;

    public function __construct($expected, $delta = 0.0)
    {
        if ($expected instanceof DateTimeInterface) {
            $this->since = $this->until = (float) $expected->format('U.u');
            $end = 'lessThanOrEqual';
        }
        else {
            [$this->since, $this->until] = date_fromto(null, $expected);
            $end = 'lessThan';
        }

        if ($delta < 0) {
            $this->since += $delta;
        }
        if ($delta > 0) {
            $this->until += $delta;
        }

        parent::__construct(LogicalAnd::fromConstraints(Assert::greaterThanOrEqual($this->since), Assert::$end($this->until)));
    }

    protected function filter($other)
    {
        try {
            $timestamp = date_timestamp($other);
            if ($timestamp === null) {
                throw new Exception('timestamp is null');
            }
            return $timestamp;
        }
        catch (Throwable $t) {
            throw new ExpectationFailedException($this->exporter()->export($other) . " is invalid datetime string.");
        }
    }

    protected function failureDescription($other): string
    {
        return sprintf("%s %s as [%s ~ %s)", $this->formatTimestamp($other), $this->toString(), $this->formatTimestamp($this->since), $this->formatTimestamp($this->until));
    }

    public function toString(): string
    {
        return 'equals datetime';
    }

    private function formatTimestamp($timestamp)
    {
        return rtrim(date_convert('Y/m/d H:i:s.v', $timestamp), '.0');
    }
}
