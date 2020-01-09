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
            return new class($object, $refmethod) implements SelfDescribing
            {
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

    public static function stringMatch($subject, $pattern, $flags = 0, $offset = 0)
    {
        $unset = function ($match) {
            $result = [];
            $keys = array_keys($match);
            for ($i = 1; $i < count($keys); $i++) {
                $key = $keys[$i];
                if (is_string($key)) {
                    $result[$key] = $match[$key];
                    $i++;
                }
                else {
                    $result[] = $match[$key];
                }
            }
            return $result;
        };

        $endpairs = [
            '(' => ')',
            '{' => '}',
            '[' => ']',
            '<' => '>',
        ];
        $endpos = strrpos($pattern, $endpairs[$pattern[0]] ?? $pattern[0]);
        $expression = substr($pattern, 0, $endpos);
        $modifiers = str_split(substr($pattern, $endpos));

        if (($g = array_search('g', $modifiers, true)) !== false) {
            unset($modifiers[$g]);

            preg_match_all($expression . implode('', $modifiers), $subject, $matches, $flags, $offset);
            if (($flags & PREG_SET_ORDER) === PREG_SET_ORDER) {
                return array_map($unset, $matches);
            }
            return $unset($matches);
        }
        else {
            $flags = ~PREG_PATTERN_ORDER & ~PREG_SET_ORDER & $flags;

            preg_match($pattern, $subject, $matches, $flags, $offset);
            return $unset($matches);
        }
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

    public static function isStringy($value): bool
    {
        return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    public static function exportVar($value): string
    {
        $INDENT = 4;

        $export = function ($value, $nest = 0, $parents = []) use (&$export, $INDENT) {
            foreach ($parents as $parent) {
                if ($parent === $value) {
                    return $export('*RECURSION*');
                }
            }
            if (is_array($value)) {
                $spacer1 = str_repeat(' ', ($nest + 1) * $INDENT);
                $spacer2 = str_repeat(' ', $nest * $INDENT);

                $hashed = array_values($value) !== $value;

                if ($hashed) {
                    $keys = array_map($export, array_combine($keys = array_keys($value), $keys));
                    $maxlen = max(array_map('strlen', $keys));
                }
                else {
                    $primitive = true;
                    foreach ($value as $k => $v) {
                        $primitive = $primitive && (is_scalar($v) || is_null($v) || is_resource($v));
                    }
                    if ($primitive) {
                        return '[' . implode(', ', array_map($export, $value)) . ']';
                    }
                }

                $kvl = '';
                $parents[] = $value;
                foreach ($value as $k => $v) {
                    /** @noinspection PhpUndefinedVariableInspection */
                    $keystr = $hashed ? $keys[$k] . str_repeat(' ', $maxlen - strlen($keys[$k])) . ' => ' : '';
                    $kvl .= $spacer1 . $keystr . $export($v, $nest + 1, $parents) . ",\n";
                }
                return "[\n{$kvl}{$spacer2}]";
            }
            elseif (is_object($value)) {
                $refclass = new \ReflectionClass($value);
                $props = get_object_vars($value);
                do {
                    foreach ($refclass->getProperties() as $property) {
                        if (!$property->isStatic()) {
                            $property->setAccessible(true);
                            $props += [$property->getName() => $property->getValue($value)];
                        }
                    }
                } while ($refclass = $refclass->getParentClass());

                $parents[] = $value;
                return get_class($value) . '::__set_state(' . $export($props, $nest, $parents) . ')';
            }
            elseif (is_null($value)) {
                return 'null';
            }
            else {
                return var_export($value, true);
            }
        };

        return $export($value) . "\n";
    }
}
