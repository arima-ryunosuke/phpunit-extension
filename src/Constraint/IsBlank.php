<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsBlank extends AbstractConstraint
{
    private $trim;

    public function __construct(bool $trim = true)
    {
        $this->trim = $trim;
    }

    protected function matches($other): bool
    {
        if (is_object($other) && method_exists($other, '__toString')) {
            $other = (string) $other;
        }

        if ($other === 0 || $other === 0.0 || $other === '0') {
            return false;
        }

        if (is_string($other) && $this->trim) {
            $other = trim($other);
        }

        return empty($other);
    }
}
