<?php

namespace ryunosuke\PHPUnit\Constraint;

class IsThrowable extends AbstractConstraint
{
    /** @var \Throwable */
    private $expected;

    public function __construct($expected = null)
    {
        if (class_exists($expected)) {
            $expected = (new \ReflectionClass($expected))->newInstanceWithoutConstructor();
        }
        elseif (is_null($expected)) {
            $expected = new \Exception();
        }
        elseif (is_string($expected)) {
            $expected = new \Exception($expected, 0);
        }
        elseif (is_int($expected)) {
            $expected = new \Exception('', $expected);
        }
        $this->expected = $expected;
    }

    protected function failureDescription($other): string
    {
        return sprintf('%s %s', $this->throwableToString($other), $this->toString());
    }

    protected function matches($other): bool
    {
        if (!$other instanceof $this->expected) {
            return false;
        }

        $expectedCode = $this->expected->getCode();
        if ($expectedCode && $expectedCode !== $other->getCode()) {
            return false;
        }

        $expectedMessage = $this->expected->getMessage();
        if (strlen($expectedMessage) && strpos($other->getMessage(), $expectedMessage) === false) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'to be ' . $this->throwableToString($this->expected);
    }
}
