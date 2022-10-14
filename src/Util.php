<?php

namespace ryunosuke\PHPUnit;

use PHPUnit\Framework\SelfDescribing;
use ryunosuke\PHPUnit\Exception\UndefinedException;

class Util
{
    public static function relativizeFile(string $filename, string $markfile): string
    {
        $dirname = $filename = realpath($filename);

        $root = null;
        while ($dirname !== dirname($dirname)) {
            if (file_exists("$dirname/$markfile")) {
                $root = $dirname;
            }
            $dirname = dirname($dirname);
        }

        if ($root === null) {
            return basename($filename);
        }

        return substr($filename, strlen($root) + 1);
    }

    public static function reflectFile(\Reflector $reflector, string $format = '%s#%d-%d'): string
    {
        assert(method_exists($reflector, 'getFilename') && method_exists($reflector, 'getStartLine') && method_exists($reflector, 'getEndLine'));
        return sprintf($format,
            self::relativizeFile($reflector->getFileName(), 'vendor'),
            $reflector->getStartLine(),
            $reflector->getEndLine()
        );
    }

    public static function propertyToValue($object, string $property)
    {
        if (is_object($object)) {
            $properties = get_object_properties($object);
            if (array_key_exists($property, $properties)) {
                return $properties[$property];
            }
        }

        $refclass = is_string($object) ? new \ReflectionClass($object) : new \ReflectionObject($object);
        for ($class = $refclass; $class; $class = $class->getParentClass()) {
            if ($class->hasProperty($property)) {
                $refproperty = $class->getProperty($property);
                $refproperty->setAccessible(true);
                return $refproperty->isStatic() ? $refproperty->getValue() : $refproperty->getValue($object);
            }
        }

        if (method_exists($object, '__get')) {
            return $object->$property;
        }

        throw new UndefinedException($refclass->name . '::$' . $property . ' is not defined.');
    }

    public static function methodToCallable($object, string $method = null): callable
    {
        $refclass = new \ReflectionClass($object);
        $method = $method ?? '__invoke';

        if ($refclass->hasMethod($method)) {
            $refmethod = $refclass->getMethod($method);
            if (!$refmethod->isConstructor() && $refmethod->isPublic()) {
                return [$object, $method];
            }
            $refmethod->setAccessible(true);
            if ($refmethod->isConstructor()) {
                if ($refmethod->isPublic()) {
                    $callable = fn(...$args) => $refclass->newInstance(...$args);
                }
                else {
                    $callable = function (...$args) use ($refclass, $refmethod) {
                        $instance = $refclass->newInstanceWithoutConstructor();
                        $refmethod->invoke($instance, ...$args);
                        return $instance;
                    };
                }
            }
            else {
                $callable = fn(...$args) => $refmethod->invoke($refmethod->isStatic() ? null : $object, ...$args);
            }
            $describe = ($refclass->isAnonymous() ? 'AnonymousClass@' . self::reflectFile($refclass) : $refclass->name) . '::' . $refmethod->name;
            return self::selfDescribingCallable($callable, $describe);
        }

        if ($method === '__construct') {
            $callable = fn(...$args) => $refclass->newInstance(...$args);
            $describe = ($refclass->isAnonymous() ? 'AnonymousClass@' . self::reflectFile($refclass) : $refclass->name) . '::' . $method;
            return self::selfDescribingCallable($callable, $describe);
        }

        if (method_exists($object, '__call') || method_exists($object, '__callStatic')) {
            return [$object, $method];
        }

        throw new UndefinedException((is_object($object) ? get_class($object) : $object) . '::' . $method . '() is not defined.');
    }

    public static function selfDescribingCallable(callable $callable, string $describing): callable
    {
        return new class($callable, $describing) implements SelfDescribing {
            private $callable;
            private $describe;

            public function __construct($callable, $describe)
            {
                $this->callable = $callable;
                $this->describe = $describe;
            }

            public function __invoke(...$args)
            {
                return ($this->callable)(...$args);
            }

            public function toString(): string
            {
                return $this->describe;
            }
        };
    }

    public static function callableToString(callable $callable): string
    {
        is_callable($callable, false, $callname);
        [$object, $method] = is_array($callable) ? $callable : [$callable, '__invoke'];

        if ($callname === 'Closure::__invoke') {
            return sprintf('Closure@%s', self::reflectFile(new \ReflectionFunction($object)));
        }

        if (strpos($callname, '@anonymous') !== false) {
            if ($object instanceof SelfDescribing) {
                return $object->toString();
            }
            else {
                return sprintf('AnonymousClass@%s::%s', self::reflectFile(new \ReflectionClass($object)), $method);
            }
        }

        return $callname;
    }
}
