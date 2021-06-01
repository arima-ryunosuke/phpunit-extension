<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsThrowable extends AbstractConstraint
{
    private $expectedClass = null;
    private $expectedMessage = '';
    private $expectedCode = 0;

    public function __construct($expected = null)
    {
        if ($expected instanceof \Throwable) {
            $this->expectedClass = get_class($expected);
            $this->expectedMessage = $expected->getMessage();
            $this->expectedCode = $expected->getCode();
        }
        elseif (class_exists($expected)) {
            $this->expectedClass = $expected;
        }
        elseif (is_string($expected)) {
            $this->expectedMessage = $expected;
        }
        elseif (is_int($expected)) {
            $this->expectedCode = $expected;
        }
    }

    protected function failureDescription($other): string
    {
        return sprintf('%s %s', $this->throwableToString($other), $this->toString());
    }

    protected function matches($other): bool
    {
        if ($this->expectedClass && !$other instanceof $this->expectedClass) {
            return false;
        }

        if ($this->expectedCode && $this->expectedCode !== $other->getCode()) {
            return false;
        }

        if (strlen($this->expectedMessage) && strpos($other->getMessage(), $this->expectedMessage) === false) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'to be ' . sprintf('\\%s(%s, %s)',
                $this->expectedClass ? $this->expectedClass : \Throwable::class,
                $this->exporter()->export($this->expectedMessage),
                $this->exporter()->export($this->expectedCode)
            );
    }
}
