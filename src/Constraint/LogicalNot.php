<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint as PHPUnitConstraint;
use PHPUnit\Framework\Constraint\Constraint;

class LogicalNot extends AbstractConstraint
{
    /** @var Constraint */
    private $constraint;

    public static function export($string)
    {
        $verbs = [
            'is',
            'equals',
            'exists',
            'contains',
            'has',
            'matches',
        ];
        $lower_verbs = implode('|', array_map('lcfirst', $verbs));
        $upper_verbs = implode('|', array_map('ucfirst', $verbs));

        $regex = implode('|', [
            "^($lower_verbs|$upper_verbs)([A-Z]|$)",
            "($upper_verbs)([A-Z]|$)",
        ]);
        if (!preg_match("#$regex#", $string)) {
            return (ctype_lower($string[0]) ? 'not' : 'Not') . ucfirst($string);
        }
        return preg_replace_callback("#$regex#", function ($matches) {
            if (isset($matches[3])) {
                if (strtolower($matches[3]) === 'is') {
                    return $matches[3] . 'Not' . $matches[4];
                }
                return 'Not' . $matches[3] . $matches[4];
            }
            else {
                if (strtolower($matches[1]) === 'is') {
                    return $matches[1] . 'Not' . $matches[2];
                }
                return (ctype_lower($matches[1][0]) ? 'not' : 'Not') . ucfirst($matches[1]) . $matches[2];
            }
        }, $string);
    }

    public static function import($string)
    {
        return preg_replace_callback("#([nN])ot([A-Z]|$)#", function ($matches) {
            return $matches[1] === 'n' ? lcfirst($matches[2]) : $matches[2];
        }, $string);
    }

    public function __construct($constraint)
    {
        $this->constraint = $constraint;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = !$this->constraint->evaluate($other, $description, true);

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }
        return null;
    }

    public function count(): int
    {
        return count($this->constraint);
    }

    public function toString(): string
    {
        switch (get_class($this->constraint)) {
            case PHPUnitConstraint\LogicalAnd::class:
            case PHPUnitConstraint\LogicalNot::class:
            case PHPUnitConstraint\LogicalOr::class:
            case LogicalAnd::class:
            case LogicalNot::class:
            case LogicalOr::class:
                return 'not( ' . $this->constraint->toString() . ' )';

            default:
                return PHPUnitConstraint\LogicalNot::negate($this->constraint->toString());
        }
    }

    protected function failureDescription($other): string
    {
        switch (get_class($this->constraint)) {
            case PHPUnitConstraint\LogicalAnd::class:
            case PHPUnitConstraint\LogicalNot::class:
            case PHPUnitConstraint\LogicalOr::class:
            case LogicalAnd::class:
            case LogicalNot::class:
            case LogicalOr::class:
                return 'not( ' . $this->constraint->failureDescription($other) . ' )';

            default:
                return PHPUnitConstraint\LogicalNot::negate($this->constraint->failureDescription($other));
        }
    }
}
