<?php

namespace ryunosuke\PHPUnit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\RegularExpression;
use ryunosuke\PHPUnit\Constraint\IsCType;
use ryunosuke\PHPUnit\Constraint\IsValid;
use ryunosuke\PHPUnit\Constraint\Throws;

if (!trait_exists(Annotation::class)) {
    trait Annotation
    {
    }
}

class Actual implements \ArrayAccess
{
    use Annotation;

    public static $constraintVariations = [
        // alias
        'isSame'               => IsIdentical::class,
        'equalsCanonicalizing' => [IsEqual::class => ['canonicalize' => true]],
        'equalsIgnoreCase'     => [IsEqual::class => [4 => true]],
        'matches'              => RegularExpression::class,
        'gt'                   => GreaterThan::class,
        'lt'                   => LessThan::class,
        'gte'                  => [IsEqual::class, GreaterThan::class],
        'lte'                  => [IsEqual::class, LessThan::class],
        'isNullOrString'       => [IsNull::class, IsType::class => [IsType::TYPE_STRING]],
        // via IsType
        'isArray'              => [IsType::class => [IsType::TYPE_ARRAY]],
        'isBool'               => [IsType::class => [IsType::TYPE_BOOL]],
        'isFloat'              => [IsType::class => [IsType::TYPE_FLOAT]],
        'isInt'                => [IsType::class => [IsType::TYPE_INT]],
        # 'isNull'        => [IsType::class => [IsType::TYPE_NULL]], // already exists internal
        'isNumeric'            => [IsType::class => [IsType::TYPE_NUMERIC]],
        'isObject'             => [IsType::class => [IsType::TYPE_OBJECT]],
        'isResource'           => [IsType::class => [IsType::TYPE_RESOURCE]],
        'isString'             => [IsType::class => [IsType::TYPE_STRING]],
        'isScalar'             => [IsType::class => [IsType::TYPE_SCALAR]],
        'isCallable'           => [IsType::class => [IsType::TYPE_CALLABLE]],
        'isIterable'           => [IsType::class => [IsType::TYPE_ITERABLE]],
        // via IsCType
        'isCtypeAlnum'         => [IsCType::class => [IsCType::CTYPE_ALNUM]],
        'isCtypeAlpha'         => [IsCType::class => [IsCType::CTYPE_ALPHA]],
        'isCtypeCntrl'         => [IsCType::class => [IsCType::CTYPE_CNTRL]],
        'isCtypeDigit'         => [IsCType::class => [IsCType::CTYPE_DIGIT]],
        'isCtypeGraph'         => [IsCType::class => [IsCType::CTYPE_GRAPH]],
        'isCtypeLower'         => [IsCType::class => [IsCType::CTYPE_LOWER]],
        'isCtypePrint'         => [IsCType::class => [IsCType::CTYPE_PRINT]],
        'isCtypePunct'         => [IsCType::class => [IsCType::CTYPE_PUNCT]],
        'isCtypeSpace'         => [IsCType::class => [IsCType::CTYPE_SPACE]],
        'isCtypeUpper'         => [IsCType::class => [IsCType::CTYPE_UPPER]],
        'isCtypeXdigit'        => [IsCType::class => [IsCType::CTYPE_XDIGIT]],
        // via IsValid
        'isValidEmail'         => [IsValid::class => [IsValid::VALID_EMAIL]],
        'isValidIp'            => [IsValid::class => [IsValid::VALID_IP]],
        'isValidIpv4'          => [IsValid::class => [IsValid::VALID_IPV4]],
        'isValidIpv6'          => [IsValid::class => [IsValid::VALID_IPV6]],
        'isValidMac'           => [IsValid::class => [IsValid::VALID_MAC]],
        'isValidUrl'           => [IsValid::class => [IsValid::VALID_URL]],
    ];

    public static $constraintNamespaces = [
        "\\ryunosuke\\PHPUnit\\Constraint\\" => __DIR__ . '/Constraint',
        "\\PHPUnit\\Framework\\Constraint\\" => __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Constraint',
    ];

    /** @var mixed testing value */
    private $actual;

    /** @var Actual */
    private $parent;

    /** @var bool */
    private $autoback;

    /** @var Throws */
    private $catch;

    public static function generateAnnotation($rawarray = false)
    {
        $annotate = function ($mname, $parameters, $defaults) {
            $result = [];
            $returnType = __CLASS__;

            $argstrings = array_filter(array_map(function (\ReflectionParameter $parameter) use ($defaults) {
                if (!$parameter->isOptional() && array_key_exists($parameter->getPosition(), $defaults)) {
                    return null;
                }
                $type = '';
                if ($parameter->hasType()) {
                    $tname = $parameter->getType()->getName();
                    $tname = (class_exists($tname) ? '\\' : '') . $tname;
                    $type = ($parameter->allowsNull() ? '?' : '') . $tname . ' ';
                }
                $arg = $type . '$' . $parameter->getName();
                if (array_key_exists($parameter->getPosition(), $defaults)) {
                    $arg .= ' = ' . var_export($defaults[$parameter->getPosition()], true);
                }
                elseif ($parameter->isDefaultValueAvailable()) {
                    $arg .= ' = ' . var_export($parameter->getDefaultValue(), true);
                }
                return $arg;
            }, $parameters), function ($v) { return $v !== null; });
            $argstring = implode(', ', $argstrings);

            // $allName = preg_replace('#^is([A-Z]+)#', 'allAre$1', $mname, 1, $count);
            $allName = "all" . ucfirst($mname);
            $result[$allName] = "\\$returnType $allName($argstring)";

            $result[$mname] = "\\$returnType $mname($argstring)";

            $notName = preg_replace('#^notIs#', 'isNot', "not" . ucfirst($mname), 1);
            $result[$notName] = "\\$returnType $notName($argstring)";

            $requiredCount = array_reduce($parameters, function ($carry, \ReflectionParameter $parameter) use ($defaults) {
                return $carry + (int) !($parameter->isOptional() || array_key_exists($parameter->getPosition(), $defaults));
            }, 0);
            if ($requiredCount) {
                $requiredParams = array_slice($parameters, 0, $requiredCount);
                $optionalParams = array_slice($argstrings, $requiredCount);
                $firstParam = implode('', array_map(function (\ReflectionParameter $parameter) {
                    return $parameter->getName();
                }, $requiredParams));
                $argstring = implode(', ', array_merge(["array \${$firstParam}s"], $optionalParams));

                $anyName = $mname . 'Any';
                $result[$anyName] = "\\$returnType $anyName($argstring)";
            }

            return $result;
        };

        $dummyConstructor = (new \ReflectionClass(new class()
        {
            public function __construct() { }
        }))->getConstructor();

        $annotations = [];

        foreach (self::$constraintNamespaces as $namespace => $directory) {
            foreach (glob("$directory/*.php") as $file) {
                $refclass = new \ReflectionClass('\\' . trim($namespace, '\\') . '\\' . basename($file, '.php'));
                if (false
                    || $refclass->isAbstract()
                    || $refclass->isInterface()
                    || strpos($refclass->getShortName(), 'Logical') === 0
                    || !is_subclass_of($refclass->name, Constraint::class)
                ) {
                    continue;
                }

                $name = lcfirst($refclass->getShortName());
                $method = $refclass->getConstructor() ?? $dummyConstructor;
                $parameters = $method ? $method->getParameters() : [];
                $via = "\\{$refclass->name}";

                $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, [])]);
            }
        }

        foreach (self::$constraintVariations as $name => $variation) {
            if ($variation instanceof Constraint) {
                $refclass = new \ReflectionClass($variation);
                $method = $refclass->getConstructor() ?? $dummyConstructor;

                $via = sprintf('%s#%d-%d', basename($refclass->getFileName()), $refclass->getStartLine(), $refclass->getEndLine());
                $parameters = $method->getParameters();
                $annotations = array_merge($annotations, [$via => $annotate($name, $parameters, [])]);
                continue;
            }

            $via = [];
            $parameters = $defaults = [];
            foreach ((array) $variation as $classname => $args) {
                if (is_int($classname)) {
                    $classname = $args;
                    $args = [];
                }
                $refclass = new \ReflectionClass($classname);
                $method = $refclass->getConstructor() ?? $dummyConstructor;

                $defaults = array_reduce($method->getParameters(), function ($carry, \ReflectionParameter $parameter) use ($args) {
                    if (array_key_exists($parameter->getName(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getName()];
                    }
                    elseif (array_key_exists($parameter->getPosition(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getPosition()];
                    }
                    return $carry;
                }, []);

                $parameters = $method->getParameters();
                $via[] = "\\{$refclass->name}::{$method->name}()" . ($args ? ' ' . json_encode($args) : '');
            }

            $annotations = array_merge($annotations, [implode(',', $via) => $annotate($name, $parameters, $defaults)]);
        }

        if ($rawarray) {
            return $annotations;
        }

        $result = [];
        foreach ($annotations as $name => $methods) {
            $result[] = ' * @see ' . $name;
            foreach ($methods as $method) {
                $result[] = ' * @method ' . $method;
            }
            $result[] = ' *';
        }
        return "/**\n" . implode("\n", $result) . "\n */";
    }

    private static function create($actual, $parent = null)
    {
        $that = new static($actual);
        $that->parent = $parent ?? $that;
        $that->autoback = !!$that->parent->autoback;
        return $that;
    }

    public function __construct($actual)
    {
        $this->actual = $actual;
        $this->parent = $this;
    }

    public function __call($name, $arguments)
    {
        $callee = $name;
        $modes = [];

        $callee = preg_replace('#^all#', '', $callee, 1, $count);
        $modes['all'] = !!$count;

        $callee = preg_replace('#^(is)?(Not|not)([A-Z])#', '$1$3', $callee, 1, $count);
        $modes['not'] = !!$count;

        $callee = preg_replace('#Any$#', '', $callee, 1, $count);
        $modes['any'] = !!$count;

        $actuals = $modes['all'] ? $this->actual : [$this->actual];

        $callee = lcfirst($callee);
        if (isset(self::$constraintVariations[$callee])) {
            $variation = self::$constraintVariations[$callee];
            if ($variation instanceof Constraint) {
                $refclass = new \ReflectionClass($variation);
                $constructor = $refclass->getConstructor();
                if ($constructor) {
                    $constructor->invokeArgs($variation, $arguments);
                }
                return $this->asserts($actuals, $variation);
            }

            $constraints = [];
            foreach ((array) $variation as $classname => $args) {
                if (is_int($classname)) {
                    $classname = $args;
                    $args = [];
                }
                $constraints[] = $this->newConstraint($classname, $arguments + $args, $modes);
            }
            return $this->asserts($actuals, ...$constraints);
        }

        $callee = ucfirst($callee);
        foreach (self::$constraintNamespaces as $namespace => $directory) {
            if (class_exists($constraintClass = trim($namespace, '\\') . '\\' . $callee)) {
                $constraint = $this->newConstraint($constraintClass, $arguments, $modes);
                return $this->asserts($actuals, $constraint);
            }
        }

        if ($this->isCallableMethod($this->actual, $name)) {
            return $this->call($name, ...$arguments);
        }

        throw new \BadMethodCallException("$name -> $callee");
    }

    public function __get($name): Actual
    {
        Assert::assertObjectHasAttribute($name, $this->actual);
        return static::create($this->actual->$name, $this);
    }

    public function offsetGet($offset): Actual
    {
        Assert::assertArrayHasKey($offset, $this->actual);
        return static::create($this->actual[$offset], $this);
    }

    public function call($name, ...$arguments)
    {
        if ($this->catch) {
            $catch = $this->catch;
            $this->catch = null;
            Assert::assertThat(array_merge([[$this->actual, $name]], $arguments), $catch);
            return $this;
        }
        return static::create($this->actual->$name(...$arguments), $this);
    }

    public function parent(int $nest = 1)
    {
        $that = $this;
        do {
            $that = $that->parent;
        } while (--$nest > 0);
        return $that;
    }

    public function autoback(bool $autoback = true)
    {
        $this->autoback = $autoback;
        return $this;
    }

    public function catch($expected)
    {
        $this->catch = new Throws($expected);
        return $this;
    }

    public function assert(Constraint ...$constraints)
    {
        return $this->asserts([$this->actual], ...$constraints);
    }

    private function asserts($actuals, Constraint ...$constraints)
    {
        $constraint = LogicalOr::fromConstraints(...$constraints);
        foreach ($actuals as $actual) {
            Assert::assertThat($actual, $constraint);
        }
        if ($this->autoback) {
            return $this->parent();
        }
        return $this;
    }

    private function newConstraint($constraintClass, $arguments, $modes): Constraint
    {
        $newConstraint = function ($args) use ($constraintClass, $modes) {
            $constructor = (new \ReflectionClass($constraintClass))->getConstructor();
            if ($constructor) {
                $args = array_reduce($constructor->getParameters(), function ($carry, \ReflectionParameter $parameter) use ($args) {
                    if (array_key_exists($parameter->getName(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getName()];
                    }
                    elseif (array_key_exists($parameter->getPosition(), $args)) {
                        $carry[$parameter->getPosition()] = $args[$parameter->getPosition()];
                    }
                    elseif ($parameter->isDefaultValueAvailable()) {
                        $carry[$parameter->getPosition()] = $parameter->getDefaultValue();
                    }
                    return $carry;
                }, []);
            }
            $constraint = new $constraintClass(...$args);
            if ($modes['not']) {
                $constraint = new LogicalNot($constraint);
            }
            return $constraint;
        };

        if ($modes['any']) {
            $constraints = [];
            $values = reset($arguments);
            foreach (is_array($values) ? $values : [$values] as $value) {
                $constraints[] = $newConstraint([$value] + $arguments);
            }
            return LogicalOr::fromConstraints(...$constraints);
        }
        else {
            return $newConstraint($arguments);
        }
    }

    private function isCallableMethod($object, string $method): bool
    {
        if (!is_object($object)) {
            return false;
        }
        if (method_exists($object, $method)) {
            return true;
        }
        if (!is_callable([$object, $method])) {
            return false;
        }

        // treat __call magic method via @method
        $ref = new \ReflectionClass($object);
        do {
            $doccomment = $ref->getDocComment();
            preg_match_all('#@method\s+([_0-9a-z\\\\|\[\]$]+\s+)?\s*?([_0-9a-z]+)\(#iums', $doccomment, $matches, PREG_SET_ORDER);
            foreach ($matches as [, , $mname]) {
                if (strcasecmp($method, $mname) === 0) {
                    return true;
                }
            }
        } while ($ref = $ref->getParentClass());

        return false;
    }

    public function __isset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function __unset($name) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function __set($name, $value) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetExists($offset) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetSet($offset, $value) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }

    public function offsetUnset($offset) { throw new \DomainException(__FUNCTION__ . ' is not supported.'); }
}
