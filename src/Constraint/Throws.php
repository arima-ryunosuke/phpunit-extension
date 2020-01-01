<?php

namespace ryunosuke\PHPUnit\Constraint;

class Throws extends AbstractConstraint
{
    /** @var \Throwable */
    private $expected, $actual;

    public function __construct($value)
    {
        if (is_string($value)) {
            if (class_exists($value)) {
                $value = (new \ReflectionClass($value))->newInstanceWithoutConstructor();
            }
            else {
                $value = new \Exception($value);
            }
        }
        $this->expected = $value;
    }

    protected function failureDescription($other): string
    {
        [, , $string] = $this->extractArgument($other);

        $base = sprintf('%s %s', $string, $this->toString());
        if ($this->actual instanceof \Throwable) {
            return $base . ' thrown ' . $this->throwableToString($this->actual);
        }
        return $base . ' not thrown';
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        [$callable, $args] = $this->extractArgument($other);

        try {
            $this->actual = null;

            $callable(...$args);
        }
        catch (\Throwable $actual) {
            $this->actual = $actual;

            if (!$actual instanceof $this->expected) {
                if ($returnResult) {
                    return false;
                }
                $this->fail($other, $description);
            }
            $expectedCode = $this->expected->getCode();
            if ($expectedCode && $expectedCode !== $actual->getCode()) {
                if ($returnResult) {
                    return false;
                }
                $this->fail($other, $description);
            }
            $expectedMessage = $this->expected->getMessage();
            if (strlen($expectedMessage) && strpos($actual->getMessage(), $expectedMessage) === false) {
                if ($returnResult) {
                    return false;
                }
                $this->fail($other, $description);
            }
            return true;
        }

        if ($returnResult) {
            return false;
        }
        return $this->fail($other, $description);
    }

    public function toString(): string
    {
        return 'should throw ' . $this->throwableToString($this->expected);
    }

    private function extractArgument($other)
    {
        if (is_callable($other)) {
            $callable = $other;
            $args = [];
        }
        else {
            $callable = $other[0];
            $args = array_slice($other, 1);
        }

        is_callable($callable, null, $name);
        $argstring = implode(', ', array_map(function ($v) {
            if (is_object($v) && !$v instanceof \JsonSerializable) {
                return '\\' . get_class($v);
            }
            return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $args));
        $caller = "$name($argstring)";

        return [$callable, $args, $caller];
    }

    private function throwableToString(\Throwable $throwable)
    {
        return sprintf('\\%s(%s, %s)',
            get_class($throwable),
            $this->exporter()->export($throwable->getMessage()),
            $this->exporter()->export($throwable->getCode())
        );
    }
}
