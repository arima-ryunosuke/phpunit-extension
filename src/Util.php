<?php

namespace ryunosuke\PHPUnit;

use PHPUnit\Framework\SelfDescribing;

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
        assert(method_exists($reflector, 'getFilename'));
        return sprintf($format,
            self::relativizeFile($reflector->getFileName(), 'vendor'),
            $reflector->getStartLine(),
            $reflector->getEndLine()
        );
    }

    public static function propertyToValue($object, string $property)
    {
        $properties = get_object_properties($object);
        if (array_key_exists($property, $properties)) {
            return $properties[$property];
        }

        $refclass = is_string($object) ? new \ReflectionClass($object) : new \ReflectionObject($object);
        do {
            if ($refclass->hasProperty($property)) {
                $refproperty = $refclass->getProperty($property);
                $refproperty->setAccessible(true);
                return $refproperty->isStatic() ? $refproperty->getValue() : $refproperty->getValue($object);
            }
        } while ($refclass = $refclass->getParentClass());

        if (method_exists($object, '__get')) {
            return $object->$property;
        }

        throw new \DomainException(get_class($object) . '::$' . $property . ' is not defined.');
    }

    public static function methodToCallable($object, string $method = null): callable
    {
        $refclass = new \ReflectionClass($object);
        $method = $method ?? '__invoke';

        if ($refclass->hasMethod($method)) {
            $refmethod = $refclass->getMethod($method);
            if ($refmethod->isPublic()) {
                return [$object, $method];
            }
            // return $refmethod->getClosure($object); // little information because string conversion is "Closure::__invoke"
            $refmethod->setAccessible(true);
            return new class($object, $refmethod) implements SelfDescribing {
                private $object;

                /** @var \ReflectionMethod */
                private $method;

                public function __construct($object, $method)
                {
                    $this->object = $object;
                    $this->method = $method;
                }

                public function __invoke()
                {
                    $object = $this->method->isStatic() ? null : $this->object;
                    return $this->method->invokeArgs($object, func_get_args());
                }

                public function toString(): string
                {
                    $decclass = $this->method->getDeclaringClass();
                    if ($decclass->isAnonymous()) {
                        $class = 'AnonymousClass@' . Util::reflectFile(new \ReflectionClass($this->object));
                    }
                    else {
                        $class = $decclass->name;
                    }
                    return $class . '::' . $this->method->name;
                }
            };
        }

        if (method_exists($object, '__call') || method_exists($object, '__callStatic')) {
            return [$object, $method];
        }

        throw new \DomainException(get_class($object) . '::' . $method . '() is not defined.');
    }

    public static function callableToString(callable $callable): string
    {
        is_callable($callable, null, $callname);
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
