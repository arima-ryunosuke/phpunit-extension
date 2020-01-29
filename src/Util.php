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

    public static function propertyToValue(object $object, string $property)
    {
        $refclass = new \ReflectionObject($object);
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

    public static function methodToCallable(object $object, string $method = null): callable
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
                    return $this->method->invokeArgs($this->object, func_get_args());
                }

                public function toString(): string
                {
                    $decclass = $this->method->getDeclaringClass();
                    $class = $decclass->isAnonymous() ? 'AnonymousClass' : $decclass->name;
                    return $class . '::' . $this->method->name;
                }
            };
        }

        if (method_exists($object, '__call')) {
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

        if (strpos($callname, 'class@anonymous') !== false) {
            if ($object instanceof SelfDescribing) {
                [$class, $method] = explode('::', $object->toString(), 2);
            }
            else {
                $class = 'AnonymousClass';
            }
            return sprintf('%s@%s::%s', $class, self::reflectFile(new \ReflectionClass($object)), $method);
        }

        return $callname;
    }

    public static function stringToStructure($string)
    {
        if (!is_string($string)) {
            return $string;
        }

        if (file_exists($string)) {
            $string = file_get_contents($string);
        }

        $xml = @simplexml_load_string($string);
        if ($xml !== false) {
            return $xml;
        }

        $json = @json_decode($string, true);
        if ($json !== null || strtolower(trim($string)) === 'null') {
            return $json;
        }

        return $string;
    }
}
