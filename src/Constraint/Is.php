<?php

namespace ryunosuke\PHPUnit\Constraint;

use DateTimeInterface;
use PHPUnit\Framework\Constraint\IsEqual as PHPUnitIsEqual;
use SplFileInfo;
use SplFileObject;
use Throwable;
use function ryunosuke\PHPUnit\date_fromto;

class Is extends Composite
{
    public function __construct($value, ?float $delta = null, bool $canonicalize = false, bool $ignoreCase = false)
    {
        $constraints = [];

        if ($delta === null) {
            if (is_float($value) || (is_numeric($value) && strpos($value, '.') !== false)) {
                $constraints[] = new ClosesTo($value);
            }
            if ($value instanceof DateTimeInterface || (is_string($value) && date_fromto(null, $value) !== null)) {
                $constraints[] = new DatetimeEquals($value);
            }
            if ($value instanceof SplFileInfo) {
                $constraints[] = new EqualsPath($value->getPathname());
            }
            if ($value instanceof SplFileObject) {
                $constraints[] = new FileEquals(file_get_contents($value->getPathname()));
            }
            if ($value instanceof Throwable) {
                $constraints[] = new IsThrowable($value);
            }
        }

        $constraints[] = new PHPUnitIsEqual($value, $delta ?? 0.0, $canonicalize, $ignoreCase);

        parent::__construct(LogicalOr::fromConstraints(...$constraints));
    }
}
