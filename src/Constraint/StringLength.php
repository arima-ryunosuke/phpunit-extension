<?php

namespace ryunosuke\PHPUnit\Constraint;

class StringLength extends AbstractConstraint
{
    private $length;
    private $multibyte;

    public function __construct(int $length, bool $multibyte = false)
    {
        $this->length = $length;
        $this->multibyte = $multibyte;
    }

    protected function matches($other): bool
    {
        if ($this->multibyte) {
            return $this->length === mb_strlen($other);
        }
        else {
            return $this->length === strlen($other);
        }
    }

    public function toString(): string
    {
        return sprintf("%slength is %s", ($this->multibyte ? 'mutibyte ' : ''), $this->length);
    }
}
