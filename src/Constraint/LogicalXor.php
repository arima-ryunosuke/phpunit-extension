<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

class LogicalXor extends AbstractConstraint
{
    /** @var Constraint[] */
    private $constraints;

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
        $lastResult = null;
        $e = null;

        foreach ($this->constraints as $constraint) {
            try {
                $constraint->evaluate($other);
                $success = $lastResult !== true;
                $lastResult = true;
            }
            catch (ExpectationFailedException $e) {
                $success = $lastResult !== false;
                $lastResult = false;
            }

            if (!$success) {
                return $returnResult ? false : $this->failLogically($constraint, $other, $description, $e);
            }
        }

        return $returnResult ? true : null;
    }

    public function toString(): string
    {
        return implode(' xor ', array_map(function (Constraint $constraint) { return $constraint->toString(); }, $this->constraints));
    }

    public function count(): int
    {
        return array_sum(array_map('count', $this->constraints));
    }
}
