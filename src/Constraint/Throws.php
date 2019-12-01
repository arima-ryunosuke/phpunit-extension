<?php

namespace ryunosuke\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

class Throws extends Constraint
{
    /** @var \Throwable */
    private $expected;

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
        list($caller, $ex) = $other;
        $base = sprintf('%s %s', $caller, $this->toString());
        if ($ex instanceof \Throwable) {
            return $base . ' thrown ' . $this->throwableToString($ex);
        }
        return $base . ' not thrown';
    }

    public function evaluate($other, $description = '', $returnResult = false)
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

        try {
            $callable(...$args);
            if ($returnResult) {
                return false;
            }
            $this->fail([$caller, null], $description);
        }
        catch (ExpectationFailedException $fail) {
            throw $fail;
        }
        catch (\Throwable $actual) {
            if (!$actual instanceof $this->expected) {
                if ($returnResult) {
                    return false;
                }
                $this->fail([$caller, $actual], $description);
            }
            $expectedCode = $this->expected->getCode();
            if ($expectedCode && $expectedCode !== $actual->getCode()) {
                if ($returnResult) {
                    return false;
                }
                $this->fail([$caller, $actual], $description);
            }
            $expectedMessage = $this->expected->getMessage();
            if (strlen($expectedMessage) && strpos($actual->getMessage(), $expectedMessage) === false) {
                if ($returnResult) {
                    return false;
                }
                $this->fail([$caller, $actual], $description);
            }
            return true;
        }
    }// @codeCoverageIgnore

    public function toString(): string
    {
        return 'should throw ' . $this->throwableToString($this->expected);
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
