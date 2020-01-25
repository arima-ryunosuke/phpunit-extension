<?php

namespace ryunosuke\PHPUnit\Constraint;

class Throws extends AbstractConstraint
{
    /** @var \Throwable[] */
    private $expected;

    /** @var \Throwable */
    private $actual;

    public function __construct(...$orValues)
    {
        $expected = [];
        foreach ($orValues as $value) {
            if ($value instanceof \Throwable) {
                $expected[] = $value;
            }
            elseif (class_exists($value)) {
                $expected[] = (new \ReflectionClass($value))->newInstanceWithoutConstructor();
            }
            elseif (is_string($value)) {
                $expected[] = new \Exception($value, 0);
            }
            elseif (is_int($value)) {
                $expected[] = new \Exception('', $value);
            }
        }
        $this->expected = $expected;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractCallable($other);

        $base = sprintf('%s %s', $string, $this->toString());
        if ($this->actual instanceof \Throwable) {
            return $base . ' thrown ' . $this->throwableToString($this->actual);
        }
        return $base . ' not thrown';
    }

    protected function matches($other): bool
    {
        [$callable, $args] = $this->extractCallable($other);

        try {
            $this->actual = null;

            $callable(...$args);
        }
        catch (\Throwable $actual) {
            $this->actual = $actual;

            foreach ($this->expected as $expected) {
                if ($this->compareThrowable($expected)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function toString(): string
    {
        $expecteds = [];
        foreach ($this->expected as $expected) {
            $expecteds[] = $this->throwableToString($expected);
        }
        return 'should throw ' . implode(' or ', $expecteds);
    }

    private function throwableToString(\Throwable $throwable)
    {
        return sprintf('\\%s(%s, %s)',
            get_class($throwable),
            $this->exporter()->export($throwable->getMessage()),
            $this->exporter()->export($throwable->getCode())
        );
    }

    private function compareThrowable(\Throwable $expected)
    {
        if (!$this->actual instanceof $expected) {
            return false;
        }

        $expectedCode = $expected->getCode();
        if ($expectedCode && $expectedCode !== $this->actual->getCode()) {
            return false;
        }

        $expectedMessage = $expected->getMessage();
        if (strlen($expectedMessage) && strpos($this->actual->getMessage(), $expectedMessage) === false) {
            return false;
        }

        return true;
    }
}
