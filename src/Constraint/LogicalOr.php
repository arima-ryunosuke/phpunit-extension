<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

class LogicalOr extends AbstractConstraint
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

    public function evaluate($other, string $description = '', bool $returnResult = false)
    {
        foreach ($this->constraints as $constraint) {
            try {
                $constraint->evaluate($other);
                return $returnResult ? true : null;
            }
            catch (ExpectationFailedException $e) {
            }
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return $returnResult ? false : $this->failLogically($constraint, $other, $description, $e);
    }

    public function count(): int
    {
        return array_sum(array_map('count', $this->constraints));
    }

    public function toString(): string
    {
        return implode(' or ', array_map(function (Constraint $constraint) { return $constraint->toString(); }, $this->constraints));
    }
}
