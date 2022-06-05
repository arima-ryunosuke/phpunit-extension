<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

class LogicalAnd extends AbstractConstraint
{
    /** @var Constraint[] */
    private $constraints;

    public static function export($string)
    {
        return $string . 'All';
    }

    public static function import($string)
    {
        return preg_replace('#All$#', '', $string);
    }

    public static function fromConstraints(Constraint ...$constraints): Constraint
    {
        assert(count($constraints) > 0);
        if (count($constraints) === 1) {
            return $constraints[0];
        }

        $constraint = new self();
        $constraint->constraints = $constraints;
        return $constraint;
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        foreach ($this->constraints as $constraint) {
            try {
                $constraint->evaluate($other);
            }
            catch (ExpectationFailedException $e) {
                return $returnResult ? false : $this->failLogically($constraint, $other, $description, $e);
            }
        }

        return $returnResult ? true : null;
    }

    public function count(): int
    {
        return array_sum(array_map('count', $this->constraints));
    }

    public function toString(): string
    {
        return implode(' and ', array_map(function (Constraint $constraint) { return $constraint->toString(); }, $this->constraints));
    }
}
